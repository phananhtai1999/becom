<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddOnRequest;
use App\Http\Requests\PaymentAddOnRequest;
use App\Http\Requests\UpdateAddOnRequest;
use App\Http\Resources\AddOnResource;
use App\Http\Resources\AddOnResourceCollection;
use App\Http\Resources\AddOnSubscriptionHistoryResourceCollection;
use App\Http\Resources\UserAddOnResource;
use App\Http\Resources\UserAddOnResourceCollection;
use App\Models\AddOn;
use App\Models\PaymentMethod;
use App\Services\AddOnService;
use App\Services\AddOnSubscriptionHistoryService;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\ConfigService;
use App\Services\PaymentService;
use App\Services\PaypalService;
use App\Services\StripeService;
use App\Services\UserAddOnService;
use App\Services\UserPlatformPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Throwable;

class AddOnController extends AbstractRestAPIController
{
    use RestShowTrait, RestIndexTrait, RestDestroyTrait;

    public function __construct(
        AddOnService  $service,
        PaypalService $paypalService,
        StripeService $stripeService,
        UserAddOnService $userAddOnService,
        AddOnSubscriptionPlanService $addOnSubscriptionPlanService,
        AddOnSubscriptionHistoryService $addOnSubscriptionHistoryService,
        ConfigService $configService,
        PaymentService $paymentService
    )
    {
        $this->service = $service;
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->userAddOnService = $userAddOnService;
        $this->configService = $configService;
        $this->paymentService = $paymentService;
        $this->addOnSubscriptionPlanService = $addOnSubscriptionPlanService;
        $this->addOnSubscriptionHistoryService = $addOnSubscriptionHistoryService;
        $this->resourceClass = AddOnResource::class;
        $this->resourceCollectionClass = AddOnResourceCollection::class;
        $this->userAddOnResourceCollectionClass = UserAddOnResourceCollection::class;
        $this->addOnSubscriptionHistoryResourceCollectionClass = AddOnSubscriptionHistoryResourceCollection::class;
    }

    /**
     * @param AddOnRequest $request
     * @return JsonResponse
     */
    public function store(AddOnRequest $request): JsonResponse
    {
        $model = $this->service->create($request->all());
        $model->permissions()->attach($request->get('permission_uuid'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function publishAddOn($id)
    {
        $addOn = $this->service->findOrFailById($id);
        $paypalProduct = $this->paypalService->createProduct($addOn);
        $stripeProduct = $this->stripeService->createProduct($addOn->name . ' add-on');
        $product = [
            'paypal' => $paypalProduct['id'],
            'stripe' => $stripeProduct['id']
        ];
        $this->service->update($addOn, [
            'payment_product_id' => $product,
            'status' => AddOn::ADD_ON_PUBLISH
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $addOn)
        );
    }

    /**
     * @param UpdateAddOnRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(UpdateAddOnRequest $request, $id)
    {
        $addOn = $this->service->findOrFailById($id);
        if ($addOn->status == AddOn::ADD_ON_PUBLISH && isset($request->price)) {
            return $this->sendJsonResponse(false, 'Can not edit price this platform', [], 403);
        }
        $this->service->update($addOn, $request->all());
        $addOn->permissions()->sync($request->get('permission_uuid'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $addOn)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function disableAddOn($id)
    {
        $addOn = $this->service->findOrFailById($id);
        $this->service->update($addOn, [
            'status' => AddOn::ADD_ON_DISABLE
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $addOn)
        );
    }

    public function paymentAddOn(PaymentAddOnRequest $request)
    {
        $checkPurchasedAddOn = $this->userAddOnService->checkPurchasedAddOn($request->get('add_on_subscription_plan_uuid'));
        if($checkPurchasedAddOn) {
            return $this->sendOkJsonResponse(['message' => 'You already have this add-on']);
        }
        $addOnSubscriptionPlan = $this->addOnSubscriptionPlanService->findOrFailById($request->get('add_on_subscription_plan_uuid'));
        $fromDate = Carbon::now();
        if ($addOnSubscriptionPlan->duration_type == AddOn::ADD_ON_DURATION_YEAR) {
            $toDate = Carbon::now()->addYears($addOnSubscriptionPlan->duration);
        } elseif ($addOnSubscriptionPlan->duration_type == AddOn::ADD_ON_DURATION_MONTH) {
            $toDate = Carbon::now()->addMonths($addOnSubscriptionPlan->duration);
        }
        $processResult = ['status' => false];
        if ($request->get('payment_method_uuid') == PaymentMethod::STRIPE && $this->configService->findConfigByKey('stripe_method')->value) {
            $processResult = $this->stripeService->processSubscriptionAddOn($addOnSubscriptionPlan, $fromDate, $toDate, $addOnSubscriptionPlan->payment_plan_id['stripe'], $request->all());
        } elseif ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL && $this->configService->findConfigByKey('paypal_method')->value) {
            $processResult = $this->paypalService->processSubscriptionAddOn($addOnSubscriptionPlan, $fromDate, $toDate, $addOnSubscriptionPlan->payment_plan_id['paypal'], $request->all());
        } else {
            $processResult['message'] = 'Your payment method is invalid';
        }
        if (!$processResult['status']) {

            return $this->sendJsonResponse(
                false,
                $processResult['message'] ?? 'failed',
                ['data' => [
                    'redirect_url' => $this->paymentService->failedPaymentSubscriptionAddOnUrl($request)
                ]]
            );
        } else {

            return $this->sendOkJsonResponse([
                'data' => [
                    'redirect_url' => $processResult['redirect_url']
                ]
            ]);
        }
    }

    public function myAddOn() {
        $myAddOn = $this->userAddOnService->findAllWhere([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->userAddOnResourceCollectionClass, $myAddOn)
        );
    }


    public function addOnSubscriptionHistory()
    {
        $subscriptionHistory = $this->addOnSubscriptionHistoryService->myAddOnSubscriptionHistories();

        return $this->sendOkJsonResponse(
            $this->addOnSubscriptionHistoryService->resourceCollectionToData($this->addOnSubscriptionHistoryResourceCollectionClass, $subscriptionHistory));
    }

    public function cancelAddOnSubscription($id)
    {
        $addOnSubscriptionHistory = $this->addOnSubscriptionHistoryService->findOneWhere([
            'add_on_subscription_plan_uuid' => $id,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        $userAddOn = $this->userAddOnService->findOneWhere([
            'add_on_subscription_plan_uuid' => $id,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        try {
            if ($addOnSubscriptionHistory->payment_method_uuid == PaymentMethod::PAYPAL) {
                $this->paypalService->cancelSubscription($addOnSubscriptionHistory->logs['id']);
            } else {
                $this->stripeService->cancelSubscription($addOnSubscriptionHistory->logs['id']);
            }
            $this->userAddOnService->update($userAddOn, ['auto_renew' => false]);

            return $this->sendOkJsonResponse(['message' => 'Successfully']);
        } catch (\Exception $exception) {

            return $this->sendBadRequestJsonResponse(['message' => $exception->getMessage()]);
        }
    }
}
