<?php

namespace App\Services;

use App\Abstracts\AbstractService;

class MomoService extends AbstractService
{
    /**
     * @param $url
     * @param $data
     * @return bool|string
     */
    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    /**
     * @param $totalPriceOrder
     * @param $order
     * @return array
     */
    public function processTransaction($totalPriceOrder, $order)
    {
        $endpoint = config('payment.momo.endpoint_create_order');
        $partnerCode = config('payment.momo.partner_code');
        $accessKey = config('payment.momo.access_key');
        $secretKey = config('payment.momo.secret_key');

        $orderId = time() . '_' . $order->getKey();
        $orderInfo = __('Payment for order') . ' ' . $orderId;
        $amount = round($totalPriceOrder);
        $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
        $redirectUrl = route('momo.successTransaction', 'findOrderId=' . $order->getKey());
        $extraData = "";
        $requestId = time() . "";
        $requestType = "captureWallet";

        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json

        if ((50000000 < $amount)) {

            return [
                'status' => false,
                'message' => __('messages.maximum_money')
            ];
        } elseif ($amount < 2000) {

            return [
                'status' => false,
                'message' => __('messages.minimum_money')
            ];
        } else {

            return [
                'status' => true,
                'redirect_url' => $jsonResult['payUrl']
            ];
        }
    }
}
