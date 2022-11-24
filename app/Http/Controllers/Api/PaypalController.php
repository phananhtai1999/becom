<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentSuccessfullyEvent;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentLogsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PaypalController extends AbstractRestAPIController
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var OrderService
     */
    protected $paymentLogsService;

    /**
     * @param OrderService $orderService
     * @param PaymentLogsService $paymentLogsService
     */
    public function __construct(
        OrderService $orderService,
        PaymentLogsService $paymentLogsService
    )
    {
        $this->orderService = $orderService;
        $this->paymentLogsService = $paymentLogsService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function successTransaction(Request $request)
    {
        $orderId = $request->get('orderId');
        $order = $this->orderService->findOneById($orderId);
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        $pendingStatus = Order::ORDER_PENDING_REQUEST_STATUS;
        $successStatus = Order::ORDER_PAYMENT_SUCCESS_STATUS;
        $paymentData = [
            "token" => $request['token'],
            "payerId" => $request['PayerID'],
        ];

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $successStatus));

            return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-completed/' . $orderId);
        } else {
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $pendingStatus));

            return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-error/' . $orderId);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelTransaction(Request $request)
    {
        $orderId = $request->get('orderId');
        $order = $this->orderService->findOneById($orderId);
        $paymentData = [
            "token" => $request['token'],
            "payerId" => $request['PayerID'],
        ];
        $pendingStatus = Order::ORDER_PENDING_REQUEST_STATUS;
        Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $pendingStatus));

        return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-cancel/' . $orderId);
    }
}
