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
    private function accessServer()
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('payment.paypal'));
        $provider->getAccessToken();

        return $provider;
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
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.successPayment', ['goBackUrl=' . $request['go_back_url'], 'userUuid=' . $userUuid, 'creditPackageUuid=' . $creditPackage->uuid, 'billingAddressUuid=' . $request['billing_address_uuid']]),
                    "cancel_url" => route('paypal.cancelPayment', ['goBackUrl=' . $request['go_back_url'], 'userUuid=' . $userUuid, 'creditPackageUuid=' . $creditPackage->uuid]),
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
        $apiContext = new ApiContext();

        return $provider->createProduct([
            'name' => $request->uuid,
            'type' => 'SERVICE',
        ], $apiContext->getRequestId());
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
        ], $apiContext->getRequestId());

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
            $subscription = $provider->createSubscription([
                "plan_id" => $plan,
                "shipping_amount" => [
                    "currency_code" => "USD",
                    "value" => "0"
                ],
                "application_context" => [
                    "return_url" => route('paypal.successPaymentSubscription', [
                            'goBackUrl=' . $request['go_back_url'],
                            'subscriptionPlanUuid=' . $subscriptionPlan->uuid,
                            'subscriptionDate=' . $subscriptionDate,
                            'userUuid=' . Auth::user()->getKey(),
                            'expirationDate=' . $expirationDate,
                            'platformPackageUuid=' . $subscriptionPlan->platform_package_uuid,
                            'billingAddressUuid=' . $request['billing_address_uuid']
                    ]),
                    "cancel_url" => route('paypal.cancelPaymentSubscription', ['goBackUrl=' . $request['go_back_url'], 'subscriptionPlanUuid=' . $subscriptionPlan->uuid,]),
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

            $subscription = $provider->createSubscription([
                "plan_id" => $plan,
                "shipping_amount" => [
                    "currency_code" => "USD",
                    "value" => "0"
                ],
                "application_context" => [
                    "return_url" => route('paypal.successPaymentSubscriptionAddOn', [
                        'goBackUrl=' . $request['go_back_url'],
                        'subscriptionDate=' . $subscriptionDate,
                        'userUuid=' . Auth::user()->getKey(),
                        'expirationDate=' . $expirationDate,
                        'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid,
                        'billingAddressUuid=' . $request['billing_address_uuid']
                    ]),
                    "cancel_url" => route('paypal.cancelPaymentSubscriptionAddOn', ['goBackUrl=' . $request['go_back_url'], 'addOnSubscriptionPlanUuid=' . $addOnSubscriptionPlan->uuid,]),
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
