<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SendNotificationSystemForPaymentEvent;
use App\Events\SubscriptionAddOnSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
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
    public function __construct(PaymentService $paymentService) {
        $this->paymentService = $paymentService;
    }
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
            Event::dispatch(new SendNotificationSystemForPaymentEvent([
                'credit_package_uuid' => $request->creditPackageUuid,
                'user_uuid' => $request->userUuid,
                'payment_method_uuid' => PaymentMethod::PAYPAL
            ], Notification::CREDIT_TYPE));
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

        $subscriptionHistoryData = $this->paymentService->getSubscriptionHistoryData($request, PaymentMethod::PAYPAL, $subscriptionData);
        $userPlatformPackageData = $this->paymentService->getUserPlatformPackageData($request);

        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            Event::dispatch(new SubscriptionSuccessEvent($request->userUuid, $subscriptionHistoryData, $userPlatformPackageData));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($subscriptionHistoryData, Notification::PACKAGE_TYPE));

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
        $addOnSubscriptionHistoryData = $this->paymentService->getAddOnSubscriptionHistoryData($request, PaymentMethod::PAYPAL, $subscriptionData);
        $userAddOnData = $this->paymentService->getUserAddOnData($request);
        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            Event::dispatch(new SubscriptionAddOnSuccessEvent($request->userUuid, $addOnSubscriptionHistoryData, $userAddOnData));
            Event::dispatch(new SendNotificationSystemForPaymentEvent($addOnSubscriptionHistoryData, Notification::ADDON_TYPE));
            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/success?go_back_url='. $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid);
        } else {

            return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/failed?go_back_url='. $request['goBackUrl'] . '&addOnSubscriptionPlanUuid=' . $request->addOnSubscriptionPlanUuid);
        }
    }

    public function cancelPaymentSubscriptionAddOn(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . 'my/profile/add-on/canceled?go_back_url='. $request['goBackUrl'] . '&addOnUuid=' . $request['addOnUuid']);
    }
}
