<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\PaymentAgainRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\Order;
use App\Services\MomoService;
use App\Services\OrderService;
use App\Services\PaypalService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Techup\ApiConfig\Services\ConfigService;
use Throwable;

class CheckoutController extends AbstractRestAPIController
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var
     */
    protected $momoService;

    /**
     * @var
     */
    protected $paypalService;

    /**
     * @var
     */
    protected $stripeService;

    /**
     * @param OrderService $orderService
     * @param MomoService $momoService
     * @param PaypalService $paypalService
     * @param StripeService $stripeService
     */
    public function __construct(
        OrderService $orderService,
        MomoService $momoService,
        PaypalService $paypalService,
        StripeService $stripeService,
        ConfigService $configService
    )
    {
        $this->orderService = $orderService;
        $this->momoService = $momoService;
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->configService = $configService;
    }

    /**
     * @param PaymentRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function checkout(PaymentRequest $request)
    {
        $credit = $request->get('credit');
        $paymentMethod = $request->get('payment_method_uuid');
        if ($credit < config('limitcredit.minimum_credit') || $credit > config('limitcredit.maximum_credit')) {
            return $this->sendValidationFailedJsonResponse([
                'error' => [
                    'credit' => __('messages.limit_maximum_and_minimum_credit')
                ]
            ]);
        }

        $totalPriceOrder = 0;
        if ($paymentMethod == Order::ORDER_MOMO_PAYMENT_METHOD) {
            $totalPriceOrder = round(($credit / config('limitcredit.convert_one_usd_to_credit')) * config('limitcredit.currency_vnd'));
        } elseif ($paymentMethod == Order::ORDER_PAYPAL_PAYMENT_METHOD || $paymentMethod == Order::ORDER_STRIPE_PAYMENT_METHOD) {
            $totalPriceOrder = round($credit / config('limitcredit.convert_one_usd_to_credit'), 2);
        }

        $order = $this->orderService->create([
            'status' => Order::ORDER_PENDING_REQUEST_STATUS,
            'credit' => $credit,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'payment_method_uuid' => $paymentMethod,
            'total_price' => $totalPriceOrder,
            'note' => $request->get('note'),
        ]);

        $processResult = ['status' => false];
        if ($paymentMethod == Order::ORDER_MOMO_PAYMENT_METHOD) {
            $processResult = $this->momoService->processTransaction($totalPriceOrder, $order);
        } elseif ($paymentMethod == Order::ORDER_PAYPAL_PAYMENT_METHOD) {
            $processResult = $this->paypalService->processTransaction($totalPriceOrder, $order);
        } elseif ($paymentMethod == Order::ORDER_STRIPE_PAYMENT_METHOD) {
            $processResult = $this->stripeService->processTransaction($totalPriceOrder, $order, $request->all());
        }

        if ($processResult['status'] == false) {
            $frontendUrl = $this->configService->findConfigByKey('payment_callback_feurl')->value ?? 'default.techup/';

            return $this->sendOkJsonResponse(['data' => [
                'message' => $processResult['message'],
                'redirect_url' => $frontendUrl . '/checkout/payment-error' . '?orderId=' . $order->getKey()
            ]]);
        } else {

            return $this->sendOkJsonResponse(['data' => ['redirect_url' => $processResult['redirect_url']]]);
        }
    }

    /**
     * @param PaymentAgainRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function paymentAgain(PaymentAgainRequest $request)
    {
        $order = $this->orderService->findOrFailById($request->get('order_id'));

        $processResult = ['status' => false];
        if ($order->payment_method_uuid == Order::ORDER_MOMO_PAYMENT_METHOD) {
            $processResult = $this->momoService->processTransaction($order->total_price, $order);
        } elseif ($order->payment_method_uuid == Order::ORDER_PAYPAL_PAYMENT_METHOD) {
            $processResult = $this->paypalService->processTransaction($order->total_price, $order);
        } elseif ($order->payment_method_uuid == Order::ORDER_STRIPE_PAYMENT_METHOD) {
            $processResult = $this->stripeService->processTransaction($order->total_price, $order, $request->all());
        }

        if ($processResult['status'] == false) {
            $frontendUrl = $this->configService->findConfigByKey('payment_callback_feurl')->value ?? 'default.techup/';

            return $this->sendOkJsonResponse(['data' => [
                'message' => $processResult['message'],
                'redirect_url' => $frontendUrl . '/checkout/payment-error/' . $order->getKey()
            ]]);
        } else {

            return $this->sendOkJsonResponse(['data' => ['redirect_url' => $processResult['redirect_url']]]);
        }
    }
}
