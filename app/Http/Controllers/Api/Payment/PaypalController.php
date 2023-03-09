<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use PayPal\Api\Payment;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;


class PaypalController extends AbstractRestAPIController
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelPayment(Request $request)
    {

        return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-cancel/' . $request->get('transactionHistoryUuid'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function successPayment(Request $request)
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        $paymentData = [
            "token" => $request['token'],
            "payerId" => $request['PayerID'],
        ];
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            Event::dispatch(new PaymentCreditPackageSuccessEvent($request->creditPackageUuid, $paymentData, $request->userUuid, PaymentMethod::PAYPAL));

            return redirect()->to(env('FRONTEND_URL') . '/payment/payment-completed/' . $request->get('userUuid'));
        } else {

            return redirect()->to(env('FRONTEND_URL') . '/payment/payment-error/' . $request->get('userUuid'));
        }
    }

    public function successPaymentSubscription(Request $request)
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $provider->updateSubscription($request['subscription_id'], ["start_time" => Carbon::now()->addMinute(1)]);

        $response = $provider->showSubscriptionDetails($request['subscription_id']);
        $subscriptionData = ["id" => $response['id']];

        $subscriptionHistory = [
            'user_uuid' => $request->userUuid,
            'subscription_plan_uuid' => $request->subscriptionPlanUuid,
            'subscription_date' => $request->subscriptionDate,
            'expiration_date' => $request->expirationDate,
            'payment_method_uuid' => PaymentMethod::PAYPAL,
            'logs' => $subscriptionData,
            'status' => 'success'
        ];
        $userPlatformPackage = [
            'user_uuid' => $request->userUuid,
            'platform_package_uuid' => $request->platformPackageUuid,
            'subscription_plan_uuid' => $request->subscriptionPlanUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            Event::dispatch(new SubscriptionSuccessEvent($request->userUuid, $subscriptionHistory, $userPlatformPackage));

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/success?subscriptionPlanId=' . $request->subscriptionPlanUuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/failed?subscriptionPlanId=' . $request->subscriptionPlanUuid);
        }
    }

    public function cancelPaymentSubscription(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/canceled?subscriptionPlanId=' . $request->subscriptionPlanUuid);
    }
}
