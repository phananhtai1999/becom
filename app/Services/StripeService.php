<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
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
//            $checkout_session = $stripe->checkout->sessions->create([
//                'line_items' => [[
//                    'price_data' => [
//                        'currency' => 'usd',
//                        'product_data' => [
//                            'name' => 'Credit-Package',
//                        ],
//                        'unit_amount' => $creditPackage->price * 100,
//                    ],
//                    'quantity' => 1,
//                ]],
//                'mode' => 'payment',
//                'success_url' => env('FRONTEND_URL') . 'my/profile/top-up/success?packageID=' . $creditPackage->uuid,
//                'cancel_url' => env('FRONTEND_URL') . 'my/profile/top-up/failed?packageID=' . $creditPackage->uuid,
//            ]);

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
                'redirect_url' => env('FRONTEND_URL') . 'my/profile/top-up/success?go_back_url='. $request['go_back_url'] .'&package_id=' . $creditPackage->uuid
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
            'name' => $request->uuid,
        ]);
    }
    public function disableProduct($id)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        return $stripe->products->update($id, [
            'active' => false,
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


    public function processSubscription($subscriptionPlan, $subscriptionDate, $expirationDate, $plan, $request)
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
            $subscriptionData = ["id" => $subscription->id];

            $subscriptionHistory = [
                'user_uuid' => Auth::user()->getKey(),
                'subscription_plan_uuid' => $subscriptionPlan->uuid,
                'subscription_date' => $subscriptionDate,
                'expiration_date' => $expirationDate,
                'payment_method_uuid' => PaymentMethod::STRIPE,
                'logs' => $subscriptionData,
                'status' => 'success'
            ];
            $userPlatformPackage = [
                'user_uuid' => Auth::user()->getKey(),
                'platform_package_uuid' => $subscriptionPlan->platform_package_uuid,
                'subscription_plan_uuid' => $subscriptionPlan->uuid,
                'expiration_date' => $expirationDate,
                'auto_renew' => true
            ];
            Event::dispatch(new SubscriptionSuccessEvent(Auth::user()->getKey(), $subscriptionHistory, $userPlatformPackage));

            return [
                'status' => true,
                'redirect_url' => env('FRONTEND_URL') . 'my/profile/upgrade/success?go_back_url='. $request['go_back_url'] .'&plan_id=' . $subscriptionPlan->uuid
            ];
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

}
