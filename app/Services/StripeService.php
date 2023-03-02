<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Support\Facades\Auth;
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
    public function processTransaction($creditPackage, $userUuid, $request)
    {
        $stripe = new StripeClient(config('payment.stripe.client_secret'));

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
                'amount' => $creditPackage->price * 100,
                'currency' => 'usd',
                'source' => $token,
                'description' => empty($request['description']) ? __('Payment incurred at') . ' ' . config('app.name') : $request['description']
            ]);
            $paymentData = ["token" => $token->id];
            Event::dispatch(new PaymentCreditPackageSuccessEvent($creditPackage->uuid, $paymentData, $userUuid, PaymentMethod::STRIPE));

            return [
                'status' => true,
                'redirect_url' => env('FRONTEND_URL') . '/payment/payment-completed/' . $userUuid
            ];

        } catch (InvalidRequestException|Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createProduct($request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        return $stripe->products->create([
            'name' => $request->get('name'),
        ]);
    }

    public function createPlan($productID, $request, $price)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        $product = $stripe->products->retrieve($productID);
        $plan = $stripe->plans->create([
            'amount' => $price * 100,
            'currency' => 'usd',
            'interval' => $request->get('duration_type'),
            'product' => $product,
        ]);

        return [
            'plan_id' => $plan->id,
        ];
    }


    public function processSubscription($membershipPackage, $fromDate, $toDate, $plan, $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        try {
            $token = $stripe->tokens->create([
                'card' => [
                    'name' => Auth::user()->name,
                    'number' => $request['card_number'],
                    'exp_month' => $request['exp_month'],
                    'exp_year' => $request['exp_year'],
                    'cvc' => $request['cvc'],
                ]
            ]);

            $customer = $stripe->customers->create([
                'email' => auth()->user()->email,
                'source' => $token
            ]);

            $subscription = $stripe->subscriptions->create([
                'customer' => $customer,
                'items' => [
                    ['price' => $plan],
                ]
            ]);

            $stripe->subscriptionSchedules->create([
                'from_subscription' => $subscription
            ]);

            return [
                'status' => true,
                'redirect_url' => env('FRONTEND_URL') . '/membership-packages/subscription-completed'
            ];
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

}
