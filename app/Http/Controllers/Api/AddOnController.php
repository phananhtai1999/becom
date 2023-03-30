<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddOnRequest;
use App\Http\Requests\PaymentAddOnRequest;
use App\Http\Requests\UpdateAddOnRequest;
use App\Http\Resources\AddOnResource;
use App\Http\Resources\AddOnResourceCollection;
use App\Http\Resources\UserAddOnResource;
use App\Http\Resources\UserAddOnResourceCollection;
use App\Models\AddOn;
use App\Models\PaymentMethod;
use App\Services\AddOnService;
use App\Services\AddOnSubscriptionPlanService;
use App\Services\PaypalService;
use App\Services\StripeService;
use App\Services\UserAddOnService;
use App\Services\UserPlatformPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class AddOnController extends AbstractRestAPIController
{
    use RestShowTrait, RestIndexTrait;

    public function __construct(
        AddOnService  $service,
        PaypalService $paypalService,
        StripeService $stripeService,
        UserAddOnService $userAddOnService,
        AddOnSubscriptionPlanService $addOnSubscriptionPlanService
    )
    {
        $this->service = $service;
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->userAddOnService = $userAddOnService;
        $this->addOnSubscriptionPlanService = $addOnSubscriptionPlanService;
        $this->resourceClass = AddOnResource::class;
        $this->resourceCollectionClass = AddOnResourceCollection::class;
        $this->userAddOnResourceCollectionClass = UserAddOnResourceCollection::class;
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
        $stripeProduct = $this->stripeService->createProduct($addOn);
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
        $addOnSubscriptionPlan = $this->addOnSubscriptionPlanService->findOrFailById($request->get('add_on_subscription_plan_uuid'));
        $fromDate = Carbon::now();
        if ($addOnSubscriptionPlan->duration_type == AddOn::ADD_ON_DURATION_YEAR) {
            $toDate = Carbon::now()->addYears($addOnSubscriptionPlan->duration);
        } elseif ($addOnSubscriptionPlan->duration_type == AddOn::ADD_ON_DURATION_MONTH) {
            $toDate = Carbon::now()->addMonths($addOnSubscriptionPlan->duration);
        }
        $processResult = ['status' => false];
        if ($request->get('payment_method_uuid') == PaymentMethod::STRIPE) {
            $processResult = $this->stripeService->processSubscriptionAddOn($addOnSubscriptionPlan, $fromDate, $toDate, $addOnSubscriptionPlan->payment_plan_id['stripe'], $request->all());
        } elseif ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL) {
            $processResult = $this->paypalService->processSubscriptionAddOn($addOnSubscriptionPlan, $fromDate, $toDate, $addOnSubscriptionPlan->payment_plan_id['paypal'], $request->all());
        }
        if (!$processResult['status']) {

            return $this->sendJsonResponse(
                false,
                $processResult['message'] ?? 'failed',
                ['data' => [
                    'redirect_url' => env('FRONTEND_URL') . 'my/profile/upgrade/failed?add_on_id=' . $addOnSubscriptionPlan->uuid
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
        $myAddOn = $this->userAddOnService->findAllWhere(['user_uuid' => auth()->user()->getKey()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->userAddOnResourceCollectionClass, $myAddOn)
        );
    }
}
