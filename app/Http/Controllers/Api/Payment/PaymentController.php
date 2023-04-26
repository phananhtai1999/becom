<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\SubscriptionSuccessEvent;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpdateCardStripeRequest;
use App\Http\Requests\UpgradeUserRequest;
use App\Http\Resources\CreditPackageHistoryResource;
use App\Http\Resources\CreditPackageHistoryResourceCollection;
use App\Http\Resources\SubscriptionHistoryResourceCollection;
use App\Http\Resources\SubscriptionPlanResourceCollection;
use App\Models\PaymentMethod;
use App\Models\UserCreditHistory;
use App\Services\ConfigService;
use App\Services\CreditPackageHistoryService;
use App\Services\SubscriptionHistoryService;
use App\Services\CreditPackageService;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use App\Services\SubscriptionPlanService;
use App\Services\UserCreditHistoryService;
use App\Services\UserPlatformPackageService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\StripeClient;

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
        SubscriptionHistoryService  $subscriptionHistoryService,
        UserCreditHistoryService    $userCreditHistoryService,
        UserPlatformPackageService  $userPlatformPackageService,
        ConfigService               $configService
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
        $this->configService = $configService;
        $this->creditPackageHistoryResourceCollection = CreditPackageHistoryResourceCollection::class;
        $this->subscriptionPlanResourceCollection = SubscriptionHistoryResourceCollection::class;
        $this->userCreditHistoryService = $userCreditHistoryService;
        $this->userPlatformPackageService = $userPlatformPackageService;
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
//        $checkPurchasedPlatform = $this->userPlatformPackageService->checkPurchasedPlatform($subscriptionPlan->platform_package_uuid);
//        $checkIncludePlatform = $this->platformPackageService->checkIncludePlatform($subscriptionPlan->platform_package_uuid);
//
//        if($checkPurchasedPlatform || $checkIncludePlatform) {
//            return $this->sendOkJsonResponse(['message' => 'You already have this platform package Or your platform package include this package']);
//        }
        $fromDate = Carbon::now();
        if ($subscriptionPlan->duration_type == 'year') {
            $toDate = Carbon::now()->addYears($subscriptionPlan->duration);
        } elseif ($subscriptionPlan->duration_type == 'month') {
            $toDate = Carbon::now()->addMonths($subscriptionPlan->duration);
        }

        $processResult = ['status' => false];
        if ($request->get('payment_method_uuid') == PaymentMethod::STRIPE && $this->configService->findConfigByKey('stripe_method')->value) {
            $processResult = $this->stripeService->processSubscription($subscriptionPlan, $fromDate, $toDate, $subscriptionPlan->payment_plan_id['stripe'], $request->all());
        } elseif ($request->get('payment_method_uuid') == PaymentMethod::PAYPAL && $this->configService->findConfigByKey('paypal_method')->value) {
            $processResult = $this->paypalService->processSubscription($subscriptionPlan, $fromDate, $toDate, $subscriptionPlan->payment_plan_id['paypal'], $request->all());
        } else {
            $processResult['message'] = 'Your payment method is invalid';
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

    /**
     * @return JsonResponse
     */
    public function cancelSubscription()
    {
        $currentSubscriptionHistory = $this->subscriptionHistoryService->currentSubscriptionHistory();
        $userPlatformPackage = $this->userPlatformPackageService->findOneWhere(['user_uuid' => auth()->user()->getKey()]);
        try {
            if ($currentSubscriptionHistory->payment_method_uuid == PaymentMethod::PAYPAL) {
                $this->paypalService->cancelSubscription($currentSubscriptionHistory->logs['id']);
            } else {
                $this->stripeService->cancelSubscription($currentSubscriptionHistory->logs['id']);
            }
            $this->userPlatformPackageService->update($userPlatformPackage, ['auto_renew' => false]);

            return $this->sendOkJsonResponse(['message' => 'Successfully']);
        } catch (\Exception $exception) {

            return $this->sendBadRequestJsonResponse(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @return void
     */
    public function renewByStripe()
    {
        new StripeClient(config('payment.stripe.client_secret'));
        $endpointSecret = config('payment.stripe.endpoint_secret');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpointSecret
            );
        } catch (\UnexpectedValueException|\Stripe\Exception\SignatureVerificationException $e) {
            $this->sendBadRequestJsonResponse();
            exit();
        }
//         Handle the event
        if ($event->type == 'invoice.payment_succeeded') {
            $invoice = $event->data->object;
            $subscriptionHistory = $this->subscriptionHistoryService->findByLog($invoice->lines->data[0]->subscription);
            $subscriptionPlan = $subscriptionHistory->subscriptionPlan;
            if ($subscriptionPlan->duration_type == 'year') {
                $expirationDate = Carbon::now()->addYears($subscriptionPlan->duration);
            } else {
                $expirationDate = Carbon::now()->addMonths($subscriptionPlan->duration);
            }
            $subscriptionHistoryData = [
                'user_uuid' => $subscriptionHistory->user_uuid,
                'subscription_plan_uuid' => $subscriptionHistory->subscription_plan_uuid,
                'subscription_date' => $subscriptionHistory->subscription_date,
                'expiration_date' => $expirationDate,
                'payment_method_uuid' => PaymentMethod::STRIPE,
                'logs' => $subscriptionHistory->logs,
                'status' => 'success'
            ];

            $userPlatformPackage = [
                'user_uuid' => $subscriptionHistory->user_uuid,
                'platform_package_uuid' => $subscriptionPlan->platform_package_uuid,
                'subscription_plan_uuid' => $subscriptionPlan->uuid,
                'expiration_date' => $expirationDate,
                'auto_renew' => true
            ];

            Event::dispatch(new SubscriptionSuccessEvent($subscriptionHistory->user_uuid, $subscriptionHistoryData, $userPlatformPackage));
        }
    }

    public function renewByPaypal()
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('payment.paypal'));
        $provider->getAccessToken();

        $payload = @file_get_contents('php://input');
        $event = \json_decode($payload, true);

        $subscription = $provider->showSubscriptionDetails($event['resource']['billing_agreement_id']);

        if ($event['event_type'] == 'PAYMENT.SALE.COMPLETED' && $subscription['billing_info']['cycle_executions'][0]['cycles_completed'] > 1) {
            $subscriptionHistory = $this->subscriptionHistoryService->findByLog($event['resource']['billing_agreement_id']);
            $subscriptionPlan = $subscriptionHistory->subscriptionPlan;
            if ($subscriptionPlan->duration_type == 'year') {
                $expirationDate = Carbon::now()->addYears($subscriptionPlan->duration);
            } else {
                $expirationDate = Carbon::now()->addMonths($subscriptionPlan->duration);
            }
            $subscriptionHistoryData = [
                'user_uuid' => $subscriptionHistory->user_uuid,
                'subscription_plan_uuid' => $subscriptionHistory->subscription_plan_uuid,
                'subscription_date' => $subscriptionHistory->subscription_date,
                'expiration_date' => $expirationDate,
                'payment_method_uuid' => PaymentMethod::STRIPE,
                'logs' => $subscriptionHistory->logs,
                'status' => 'success'
            ];

            $userPlatformPackage = [
                'user_uuid' => $subscriptionHistory->user_uuid,
                'platform_package_uuid' => $subscriptionPlan->platform_package_uuid,
                'subscription_plan_uuid' => $subscriptionPlan->uuid,
                'expiration_date' => $expirationDate,
                'auto_renew' => true
            ];

            Event::dispatch(new SubscriptionSuccessEvent($subscriptionHistory->user_uuid, $subscriptionHistoryData, $userPlatformPackage));
        }
    }
}
