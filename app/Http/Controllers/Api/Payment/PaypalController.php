<?php

namespace App\Http\Controllers\Api\Payment;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
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

        if (isset($response['status']) && $response['status'] == 'ACTIVE') {

            return redirect()->to(env('FRONTEND_URL') . '/membership-packages/payment-completed');
        } else {

            return redirect()->to(env('FRONTEND_URL') . '/membership-packages/payment-error');
        }
    }

    public function cancelPaymentSubscription(Request $request)
    {
        return redirect()->to(env('FRONTEND_URL') . '/membership-packages/payment-cancel');
    }
}
