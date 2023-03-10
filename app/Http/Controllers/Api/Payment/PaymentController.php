<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpgradeUserRequest;
use App\Http\Resources\CreditPackageHistoryResource;
use App\Http\Resources\CreditPackageHistoryResourceCollection;
use App\Http\Resources\SubscriptionHistoryResourceCollection;
use App\Http\Resources\SubscriptionPlanResourceCollection;
use App\Models\PaymentMethod;
use App\Services\CreditPackageHistoryService;
use App\Services\SubscriptionHistoryService;
use App\Services\CreditPackageService;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use App\Services\SubscriptionPlanService;
use App\Services\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends AbstractRestAPIController
{
    public function __construct(
        PaypalService               $paypalService,
        StripeService               $stripeService,
        UserService                 $userService,
        SubscriptionPlanService     $subscriptionPlanService,
        PlatformPackageService      $platformPackageService,
        CreditPackageService        $creditPackageService,
        CreditPackageHistoryService $creditPackageHistoryService,
        SubscriptionHistoryService  $subscriptionHistoryService
    )
    {
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->userService = $userService;
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->platformPackageService = $platformPackageService;
        $this->creditPackageService = $creditPackageService;
        $this->creditPackageHistoryService = $creditPackageHistoryService;
        $this->subscriptionHistoryService = $subscriptionHistoryService;
        $this->creditPackageHistoryResourceCollection = CreditPackageHistoryResourceCollection::class;
        $this->subscriptionPlanResourceCollection = SubscriptionHistoryResourceCollection::class;
    }

    public function topUp(PaymentRequest $request)
    {
        try {
            $creditPackage = $this->creditPackageService->findOrFailById($request->get('credit_package_uuid'));
            if ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL) {
                $processResult = $this->paypalService->processTransaction($creditPackage, Auth::user()->getKey(), $request->all());
            } else {
                $processResult = $this->stripeService->processTransaction($creditPackage, Auth::user()->getKey(), $request->all());
            }

            if ($processResult['status']) {

                return $this->sendOkJsonResponse(['data' => ['redirect_url' => $processResult['redirect_url']]]);
            } else {

                return $this->sendJsonResponse(false, $processResult['message'], [], $processResult['status_code'] ?? 400);
            }

        } catch (\Exception $exception) {
            return $this->sendJsonResponse(false, $exception->getMessage(), [], 400);
        }

    }

    public function upgradeUser(UpgradeUserRequest $request)
    {
        $subscriptionPlan = $this->subscriptionPlanService->findOrFailById($request->get('subscription_plan_uuid'));
        $plan = json_decode($subscriptionPlan->payment_plan_id);
        $platformPackage = $this->platformPackageService->findOrFailById($subscriptionPlan->platform_package_uuid);
        $fromDate = Carbon::now();
        if ($subscriptionPlan->duration_type == 'year') {
            $toDate = Carbon::now()->addYears($subscriptionPlan->duration);
        } elseif ($subscriptionPlan->duration_type == 'month') {
            $toDate = Carbon::now()->addMonths($subscriptionPlan->duration);
        }

        $processResult = ['status' => false];
        if ($request->get('payment_method_uuid') == PaymentMethod::STRIPE) {
            $processResult = $this->stripeService->processSubscription($subscriptionPlan, $fromDate, $toDate, $plan->stripe, $request->all());
        } elseif ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL) {
            $processResult = $this->paypalService->processSubscription($subscriptionPlan, $fromDate, $toDate, $plan->paypal, $request->all());
        }
        if (!$processResult['status']) {

            return $this->sendJsonResponse(
                false,
                $processResult['message'] ?? 'failed',
                ['data' => [
                    'redirect_url' => env('FRONTEND_URL') . 'my/profile/upgrade/failed?plan_id=' . $subscriptionPlan->uuid
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

    public function topUpHistory()
    {
        $topUpHistory = $this->creditPackageHistoryService->myTopUpHistories();

        return $this->sendOkJsonResponse(
            $this->creditPackageHistoryService->resourceCollectionToData($this->creditPackageHistoryResourceCollection, $topUpHistory));
    }

    public function subscriptionHistory()
    {
        $subscriptionHistory = $this->subscriptionHistoryService->mySubscriptionHistories();

        return $this->sendOkJsonResponse(
            $this->subscriptionHistoryService->resourceCollectionToData($this->subscriptionPlanResourceCollection, $subscriptionHistory));
    }
}
