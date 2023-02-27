<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpgradeUserRequest;
use App\Models\PaymentMethod;
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
        PaypalService           $paypalService,
        StripeService           $stripeService,
        UserService             $userService,
        SubscriptionPlanService $subscriptionPlanService,
        PlatformPackageService  $platformPackageService
    )
    {
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->userService = $userService;
        $this->subscriptionPlanService = $subscriptionPlanService;
        $this->platformPackageService = $platformPackageService;
    }

    public function payment(PaymentRequest $request)
    {

        try {
            if ($request->get('payment_method') == PaymentMethod::PAYPAL) {
                $processResult = $this->paypalService->processTransaction($request->get('price'), Auth::user()->getKey());
            } elseif ($request->get('payment_method') == PaymentMethod::STRIPE) {
                $processResult = $this->stripeService->processTransaction($request->get('price'), Auth::user()->getKey(), $request->all());
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
            $processResult = $this->stripeService->processSubscription($platformPackage, $fromDate, $toDate, $plan->stripe, $request->all());
        } elseif ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL) {
            $processResult = $this->paypalService->processSubscription($platformPackage, $fromDate, $toDate, $plan->paypal);
        }
        if (!$processResult['status']) {

            return $this->sendOkJsonResponse(['data' => [
                'message' => $processResult['message'],
                'redirect_url' => env('FRONTEND_URL') . '/membership-packages/subscription-failed/' . 1
            ]]);
        } else {

            return $this->sendOkJsonResponse([
                'data' => [
                    'redirect_url' => $processResult['redirect_url']
                ]
            ]);
        }
    }
}
