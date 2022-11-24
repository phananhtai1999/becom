<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PaypalService extends AbstractService
{
    /**
     * @param $totalPriceCart
     * @param $order
     * @return array
     * @throws Throwable
     */
    public function processTransaction($totalPriceCart, $order)
    {
        try {
            $provider = new PayPalClient();
            $provider->setApiCredentials(config('payment.paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.successTransaction', 'orderId=' . $order->getKey()),
                    "cancel_url" => route('paypal.cancelTransaction', 'orderId=' . $order->getKey()),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => round($totalPriceCart, 2),
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
}
