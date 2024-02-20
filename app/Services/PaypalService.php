<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use Illuminate\Support\Facades\Auth;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;
use PayPal\Rest\ApiContext;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PaypalService extends AbstractService
{
    public function accessServer()
    {
        $paypalConfig = [
            'mode' => $this->getConfigByKeyInCache('payment_mode')->value, // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
            'sandbox' => [
                'client_id' => $this->getConfigByKeyInCache('paypal_client_id')->value,
                'client_secret' => $this->getConfigByKeyInCache('paypal_client_secret')->value,
                'app_id' => $this->getConfigByKeyInCache('paypal_app_id')->value,
            ],
            'live' => [
                'client_id' => $this->getConfigByKeyInCache('paypal_client_id')->value,
                'client_secret' => $this->getConfigByKeyInCache('paypal_client_secret')->value,
                'app_id' => $this->getConfigByKeyInCache('paypal_app_id')->value,
            ],

            'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // Can only be 'Sale', 'Authorization' or 'Order'
            'currency' => env('PAYPAL_CURRENCY', 'USD'),
            'notify_url' => env('PAYPAL_NOTIFY_URL', ''), // Change this accordingly for your application.
            'locale' => env('PAYPAL_LOCALE', 'en_US'), // force gateway language  i.e. it_IT, es_ES, en_US ... (for express paypal only)
            'validate_ssl' => env('PAYPAL_VALIDATE_SSL', true), // Validate SSL when creating api client.
        ];
        $provider = new PayPalClient($paypalConfig);
        $provider->getAccessToken();

        return $provider;
    }

    private function getCallbackSuccessPaymentUrl($request, $userUuid, $creditPackage)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.successPayment', [
            'goBackUrl=' . $request['go_back_url'],
                'userUuid=' . $userUuid,
                'creditPackageUuid=' . $creditPackage->uuid,
                'billingAddressUuid=' . $request['billing_address_uuid']
            ], false);

        return str_replace('/api/', '', $url);
    }

    private function getCallbackCancelPaymentUrl($request, $userUuid, $creditPackage)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.cancelPayment', [
                'goBackUrl=' . $request['go_back_url'],
                'userUuid=' . $userUuid,
                'creditPackageUuid=' . $creditPackage->uuid,
            ], false);

        return str_replace('/api/', '', $url);
    }

    //subscription
    private function getCallbackSuccessSubscriptionUrl($request, $subscriptionPlan, $subscriptionDate, $expirationDate)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.successPaymentSubscription', [
                'goBackUrl=' . $request['go_back_url'],
                'subscriptionPlanUuid=' . $subscriptionPlan->uuid,
                'subscriptionDate=' . $subscriptionDate,
                'userUuid=' . auth()->userId(),
                'expirationDate=' . $expirationDate,
                'platformPackageUuid=' . $subscriptionPlan->app_uuid,
                'billingAddressUuid=' . $request['billing_address_uuid']
            ], false);

        return str_replace('/api/', '', $url);
    }

    private function getCallbackCancelSubscriptionUrl($request, $subscriptionPlan)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.cancelPaymentSubscription', [
                'goBackUrl=' . $request['go_back_url'],
                'subscriptionPlanUuid=' . $subscriptionPlan->uuid
            ], false);

        return str_replace('/api/', '', $url);
    }

    //subscription add-on
    private function getCallbackSuccessSubscriptionAddOnUrl($request, $addOnSubscriptionPlan, $subscriptionDate, $expirationDate)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.successPaymentSubscriptionAddOn', [
                'goBackUrl=' . $request['go_back_url'],
                'subscriptionDate=' . $subscriptionDate,
                'userUuid=' . auth()->userId(),
                'expirationDate=' . $expirationDate,
                'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid,
                'billingAddressUuid=' . $request['billing_address_uuid']
            ], false);

        return str_replace('/api/', '', $url);
    }

    private function getCallbackCancelSubscriptionAddOnUrl($request, $addOnSubscriptionPlan)
    {
        $url = env('DOMAIN') . '/' . auth()->appId() . '/' . env('SERVICE_NAME') . '/' . route('paypal.cancelPaymentSubscriptionAddOn', [
                'goBackUrl=' . $request['go_back_url'],
                'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid
            ], false);

        return str_replace('/api/', '', $url);
    }

    /**
     * @param $creditPackage
     * @param $userUuid
     * @param $request
     * @return array|void
     * @throws Throwable
     */
    public function processTransaction($creditPackage, $userUuid, $request)
    {
        try {
            $provider = $this->accessServer();
            $callbackSuccessUrl = $this->getCallbackSuccessPaymentUrl($request, $userUuid, $creditPackage);
            $callbackCancelUrl = $this->getCallbackCancelPaymentUrl($request, $userUuid, $creditPackage);
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => $callbackSuccessUrl,
                    "cancel_url" => $callbackCancelUrl,
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => round($creditPackage->price, 2),
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                // redirect to approve href
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {

                        return [
                            'status' => true,
                            'redirect_url' => $links['href']
                        ];
                    }
                }
            }

            if ($response['type'] == 'error') {

                return [
                    'status' => false,
                    'message' => $response['message']
                ];
            }
        } catch (PayPalConfigurationException|PayPalConnectionException|PayPalInvalidCredentialException|PayPalMissingCredentialException $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createProduct($request)
    {
        $provider = $this->accessServer();

        return $provider->createProduct([
            'name' => $request->uuid,
            'type' => 'SERVICE',
        ], 'create-product-' . time());
    }

    /**
     * @param $product_id
     * @param $request
     * @param $price
     * @param $name
     * @return array
     * @throws Throwable
     */
    public function createPlan($product_id, $request, $price, $name)
    {
        $apiContext = new ApiContext();
        $provider = $this->accessServer();
        $plan = $provider->createPlan([
            "product_id" => $product_id,
            'name' => $name,
            "payment_preferences" => [
                "auto_bill_outstanding" => true,
                "setup_fee" => [
                    "value" => "0",
                    "currency_code" => "USD"
                ],
            ],
            "billing_cycles" => [[
                "frequency" => [
                    "interval_unit" => $request->get('duration_type'),
                    "interval_count" => $request->get('duration', 1)
                ],
                "tenure_type" => "REGULAR",
                "sequence" => 1,
                "total_cycles" => 0,
                "pricing_scheme" => [
                    "fixed_price" => [
                        "value" => $price,
                        "currency_code" => "USD"
                    ]
                ]
            ]],
        ], 'create-plan-' . time());

        return [
            'plan_id' => $plan['id'],
        ];
    }

    /**
     * @throws Throwable
     */
    public function processSubscription($subscriptionPlan, $subscriptionDate, $expirationDate, $plan, $request)
    {
        try {
            $provider = $this->accessServer();
            $successUrl = $this->getCallbackSuccessSubscriptionUrl($request, $subscriptionPlan, $subscriptionDate, $expirationDate);
            $cancelUrl = $this->getCallbackCancelSubscriptionUrl($request, $subscriptionPlan);
            $subscription = $provider->createSubscription([
                "plan_id" => $plan,
                "shipping_amount" => [
                    "currency_code" => "USD",
                    "value" => "0"
                ],
                "application_context" => [
                    "return_url" => $successUrl,
//                    "return_url" => $this->getConfigByKeyInCache('success_url')->value . '?' . http_build_query([
//                            'goBackUrl' => $request['go_back_url'],
//                            'subscriptionPlanUuid' => $subscriptionPlan->uuid,
//                            'subscriptionDate' => $subscriptionDate,
//                            'userUuid' => auth()->userId(),
//                            'expirationDate' => $expirationDate,
//                            'platformPackageUuid' => $subscriptionPlan->platform_package_uuid,
//                            'billingAddressUuid' => $request['billing_address_uuid'],
//                        ]),
                    "cancel_url" => $cancelUrl,
                ],
            ]);
            if (isset($subscription['id']) && $subscription['id'] != null) {
                foreach ($subscription['links'] as $links) {
                    if ($links['rel'] == 'approve') {

                        return [
                            'status' => true,
                            'redirect_url' => $links['href']
                        ];
                    }
                }
            }
            if ($subscription['type'] == 'error') {

                return [
                    'status' => false,
                    'message' => $subscription['message']
                ];
            }
        } catch (PayPalConfigurationException|PayPalConnectionException|PayPalInvalidCredentialException|PayPalMissingCredentialException $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param mixed $id
     * @return void
     * @throws Throwable
     */
    public function cancelSubscription($id)
    {
        $provider = $this->accessServer();
        $provider->cancelSubscription($id, 'Cancel Subscription');
    }

    public function processSubscriptionAddOn($addOnSubscriptionPlan, $subscriptionDate, $expirationDate, $plan, $request)
    {
        try {
            $provider = $this->accessServer();
            $successUrl = $this->getCallbackSuccessSubscriptionAddOnUrl($request, $addOnSubscriptionPlan, $subscriptionDate, $expirationDate);
            $cancelUrl = $this->getCallbackCancelSubscriptionAddOnUrl($request, $addOnSubscriptionPlan);
            $subscription = $provider->createSubscription([
                "plan_id" => $plan,
                "shipping_amount" => [
                    "currency_code" => "USD",
                    "value" => "0"
                ],
                "application_context" => [
                    "return_url" => $successUrl,
                    "cancel_url" => $cancelUrl,
                ],
            ]);
            if (isset($subscription['id']) && $subscription['id'] != null) {
                foreach ($subscription['links'] as $links) {
                    if ($links['rel'] == 'approve') {

                        return [
                            'status' => true,
                            'redirect_url' => $links['href']
                        ];
                    }
                }
            }
            if ($subscription['type'] == 'error') {

                return [
                    'status' => false,
                    'message' => $subscription['message']
                ];
            }
        } catch (PayPalConfigurationException|PayPalConnectionException|PayPalInvalidCredentialException|PayPalMissingCredentialException $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
