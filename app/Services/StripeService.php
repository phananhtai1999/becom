<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\PaymentCreditPackageSuccessEvent;
use App\Events\SubscriptionAddOnSuccessEvent;
use App\Events\SubscriptionSuccessEvent;
use App\Models\PaymentMethod;
use Exception;
use http\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class StripeService extends AbstractService
{
    /**
     * @return StripeClient
     */
    public function getStripeClient(): StripeClient
    {
        return new StripeClient($this->getConfigByKeyInCache('stripe_secret_key')->value);
    }

    /**
     * @param $totalPriceCart
     * @param $order
     * @param $request
     * @return array
     */
    public function processTransaction($creditPackage, $userUuid, $request)
    {
        $stripe = $this->getStripeClient();

        try {
            $checkout_session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Credit-Package',
                        ],
                        'unit_amount' => $creditPackage->price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                "success_url" => route('stripe.successPayment', [
                    'goBackUrl=' . $request['go_back_url'],
                    'userUuid=' . $userUuid,
                    'creditPackageUuid=' . $creditPackage->uuid,
                    'billingAddressUuid=' . $request['billing_address_uuid'],
                ]) . '&session_id={CHECKOUT_SESSION_ID}',
                "cancel_url" => route('stripe.cancelPayment', ['goBackUrl=' . $request['go_back_url'], 'userUuid=' . $userUuid, 'creditPackageUuid=' . $creditPackage->uuid]),
            ]);
            if (isset($checkout_session)) {

                return [
                    'status' => true,
                    'redirect_url' => $checkout_session->url
                ];
            }

            return [
                'status' => true,
                'redirect_url' => env('FRONTEND_URL') . 'my/profile/top-up/success?go_back_url=' . $request['go_back_url'] . '&package_id=' . $creditPackage->uuid
            ];

        } catch (InvalidRequestException|Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createProduct($name)
    {
        $stripe = $this->getStripeClient();

        return $stripe->products->create([
            'name' => $name,
        ]);
    }

    public function disableProduct($id)
    {
        $stripe = $this->getStripeClient();

        return $stripe->products->update($id, [
            'active' => false,
        ]);
    }

    public function createPlan($productID, $request, $price)
    {
        $stripe = $this->getStripeClient();

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
        $stripe = $this->getStripeClient();
        try {
            $checkout_session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price' => $plan,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('stripe.successPaymentSubscription', [
                        'goBackUrl=' . $request['go_back_url'],
                        'subscriptionPlanUuid=' . $subscriptionPlan->uuid,
                        'subscriptionDate=' . $subscriptionDate,
                        'userUuid=' . Auth::user()->getKey(),
                        'expirationDate=' . $expirationDate,
                        'platformPackageUuid=' . $subscriptionPlan->platform_package_uuid,
                        'billingAddressUuid=' . $request['billing_address_uuid'],
                    ]) . '&session_id={CHECKOUT_SESSION_ID}',
//                'success_url' => $this->getConfigByKeyInCache('success_url')->value . '?' . http_build_query([
//                        'goBackUrl' => $request['go_back_url'],
//                        'subscriptionPlanUuid' => $subscriptionPlan->uuid,
//                        'subscriptionDate' => $subscriptionDate,
//                        'userUuid' => Auth::user()->getKey(),
//                        'expirationDate' => $expirationDate,
//                        'platformPackageUuid' => $subscriptionPlan->platform_package_uuid,
//                        'billingAddressUuid' => $request['billing_address_uuid'],
//                    ]) . '&session_id={CHECKOUT_SESSION_ID}',
                "cancel_url" => route('stripe.cancelPaymentSubscription', ['goBackUrl=' . $request['go_back_url'], 'subscriptionPlanUuid=' . $subscriptionPlan->uuid,]),
            ]);
            if (isset($checkout_session)) {

                return [
                    'status' => true,
                    'redirect_url' => $checkout_session->url
                ];
            }
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function processSubscriptionAddOn($addOnSubscriptionPlan, $subscriptionDate, $expirationDate, $plan, $request)
    {
        $stripe = $this->getStripeClient();
        try {
            $checkout_session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price' => $plan,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('stripe.successPaymentSubscriptionAddOn', [
                        'goBackUrl=' . $request['go_back_url'],
                        'subscriptionDate=' . $subscriptionDate,
                        'userUuid=' . Auth::user()->getKey(),
                        'expirationDate=' . $expirationDate,
                        'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid,
                        'billingAddressUuid=' . $request['billing_address_uuid']
                    ]) . '&session_id={CHECKOUT_SESSION_ID}',
                "cancel_url" => route('paypal.cancelPaymentSubscriptionAddOn', ['goBackUrl=' . $request['go_back_url'], 'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid,]),
            ]);
            if (isset($checkout_session)) {

                return [
                    'status' => true,
                    'redirect_url' => $checkout_session->url
                ];
            }
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param $id
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function cancelSubscription($id)
    {
        $stripe = $this->getStripeClient();
        $stripe->subscriptions->cancel($id);
    }

    /**
     * @param $request
     * @param $plan
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function subscription($request, $plan)
    {
        $stripe = $this->getStripeClient();

        $token = $this->createNewToken($request);

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

        return ["id" => $subscription->id];
    }

    public function createNewToken($request)
    {
        $stripe = $this->getStripeClient();

        return $stripe->tokens->create([
            'card' => [
                'name' => $request['card_name'],
                'number' => $request['card_number'],
                'exp_month' => $request['exp_month'],
                'exp_year' => $request['exp_year'],
                'cvc' => $request['cvc'],
            ]
        ]);
    }

    public function updateCustomerCard($customerId, $token)
    {
        $stripe = $this->getStripeClient();

        return $stripe->customers->update($customerId, [
            'source' => $token
        ]);
    }

    public function addCard($customerId, $token)
    {
        $stripe = $this->getStripeClient();

        return $stripe->customers->createSource($customerId, [
            'source' => $token
        ]);
    }

    public function allCard($customerId)
    {
        $stripe = $this->getStripeClient();
        $defaultSourceId = $stripe->customers->retrieve($customerId)->default_source;
        $sources = $stripe->customers->allSources($customerId);
        foreach ($sources as $source) {
            if ($source->id == $defaultSourceId) {
                $source->is_default = true;
                continue;
            }
            $source->is_default = false;
        }

        return $sources;
    }

    public function deleteCard($customerId, $id)
    {
        $stripe = $this->getStripeClient();

        return $stripe->customers->deleteSource($customerId, $id);
    }

    public function updateOneCard($customerId, $request, $id)
    {
        $stripe = $this->getStripeClient();
        $token = $this->createNewToken($request);
        $this->deleteCard($customerId, $id);

        return $stripe->customers->createSource($customerId, ['source' => $token]);
    }
}
