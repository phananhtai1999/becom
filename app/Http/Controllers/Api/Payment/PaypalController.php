<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SubscriptionAddOnSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

        return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/cancel?packageID=' . $request->creditPackageUuid);
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

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/success?go_back_url='. $request->goBackUrl .'&package_id=' . $request->creditPackageUuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/top-up/failed?go_back_url='. $request->goBackUrl .'&package_id=' . $request->creditPackageUuid);
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

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/success?go_back_url='. $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/failed?go_back_url='. $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
        }
    }

    public function cancelPaymentSubscription(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/upgrade/canceled?go_back_url='. $request['goBackUrl'] . '&plan_id=' . $request->subscriptionPlanUuid);
    }

    public function successPaymentSubscriptionAddOn(Request $request)
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $provider->updateSubscription($request['subscription_id'], ["start_time" => Carbon::now()->addMinute(1)]);

        $response = $provider->showSubscriptionDetails($request['subscription_id']);
        $subscriptionData = ["id" => $response['id']];
        $subscriptionHistoryData = [
            'user_uuid' => $request->userUuid,
            'add_on_uuid' => $request->addOnUuid,
            'subscription_date' => $request->subscriptionDate,
            'expiration_date' => $request->expirationDate,
            'payment_method_uuid' => PaymentMethod::PAYPAL,
            'logs' => $subscriptionData,
        ];
        $userAddOnData = [
            'user_uuid' => $request->userUuid,
            'add_on_uuid' => $request->addOnUuid,
            'expiration_date' => $request->expirationDate,
            'auto_renew' => true
        ];
        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            Event::dispatch(new SubscriptionAddOnSuccessEvent($request->userUuid, $subscriptionHistoryData, $userAddOnData));

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/success?go_back_url='. $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/failed?go_back_url='. $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
        }
    }

    public function cancelPaymentSubscriptionAddOn(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/canceled?go_back_url='. $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
    }
}
