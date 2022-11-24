<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\PaymentSuccessfullyEvent;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Event;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class StripeService extends AbstractService
{
    /**
     * @param $totalPriceCart
     * @param $order
     * @param $request
     * @return array
     */
    public function processTransaction($totalPriceCart, $order, $request)
    {
        $stripe = new StripeClient(config('payment.stripe.client_secret'));
        $pendingStatus = Order::ORDER_PENDING_REQUEST_STATUS;
        $successStatus = Order::ORDER_PAYMENT_SUCCESS_STATUS;

        try {
            $token = $stripe->tokens->create([
                'card' => [
                    'name' => $request['card_name'],
                    'number' => $request['card_number'],
                    'exp_month' => $request['exp_month'],
                    'exp_year' => $request['exp_year'],
                    'cvc' => $request['cvc'],
                ]
            ]);

            $stripe->charges->create([
                'amount' => $totalPriceCart * 100,
                'currency' => 'usd',
                'source' => $token,
                'description' => empty($request['description']) ? __('Payment incurred at') . ' ' . config('app.name') : $request['description']
            ]);

            $paymentData = ["token" => $token->id];
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $successStatus));

            return [
                'status' => true,
                'redirect_url' => env('FRONTEND_URL') . '/checkout/payment-completed/' . $order->getKey()
            ];

        } catch (InvalidRequestException|Exception $e) {
            $paymentData = ["message" => $e->getMessage()];
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $pendingStatus));

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
