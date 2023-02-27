<?php

namespace App\Services;

use App\Abstracts\AbstractService;
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
     * @param $totalPriceCart
     * @param $order
     * @return array
     * @throws Throwable
     */
    public function processTransaction($price, $userUuid)
    {
        try {
            $provider = new PayPalClient();
            $provider->setApiCredentials(config('payment.paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.successPayment', 'userUuid=' . $userUuid),
                    "cancel_url" => route('paypal.cancelPayment', 'userUuid=' . $userUuid),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => round($price, 2),
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
            'name' => $request->get('name'),
            'type' => 'SERVICE',
        ], $apiContext->getRequestId());
    }

    /**
     * @param $request
     * @return array
     * @throws Throwable
     */
    public function createPlan($product_id, $request, $price)
    {
        $apiContext = new ApiContext();
        $provider = $this->accessServer();
        $plan = $provider->createPlan([
            "product_id" => $product_id,
            'name' => $request->get('duration_type'),
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
                    "interval_count" => $request->get('duration') ? $request->get('duration') : 1
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
    public function processSubscription($membershipPackage, $fromDate, $toDate, $plan)
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
                    "return_url" => route('paypal.successPaymentSubscription', 'membershipPackageId=' . 1 . '&fromDate=' . $fromDate . '&toDate=' . $toDate),
                    "cancel_url" => route('paypal.cancelPaymentSubscription'),
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
