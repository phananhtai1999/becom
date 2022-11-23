<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\PaymentSuccessfullyEvent;
use App\Models\Order;
use App\Services\MomoService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class MomoController extends AbstractRestAPIController
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
     * @param OrderService $orderService
     * @param MomoService $momoService
     */
    public function __construct
    (
        OrderService $orderService,
        MomoService $momoService
    )
    {
        $this->momoService = $momoService;
        $this->orderService = $orderService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function successTransaction(Request $request)
    {
        $orderId = $request->get('orderId');
        $findOrderId = $request->get('findOrderId');
        $order = $this->orderService->findOneById($findOrderId);

        //Query status
        $endpoint = config('payment.momo.endpoint_query_status');
        $partnerCode = config('payment.momo.partner_code');
        $accessKey = config('payment.momo.access_key');
        $secretKey = config('payment.momo.secret_key');
        $requestId = time() . "";

        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&orderId=" . $orderId . "&partnerCode=" . $partnerCode . "&requestId=" . $requestId;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'signature' => $signature,
            'lang' => 'vi'
        ];

        $result = $this->momoService->execPostRequest($endpoint, json_encode($data));
        $paymentStatus = json_decode($result, true);  // decode json
        $pendingStatus = Order::ORDER_PENDING_REQUEST_STATUS;
        $successStatus = Order::ORDER_PAYMENT_SUCCESS_STATUS;

        $paymentData = [
            'endpoint' => $endpoint,
            'accessKey' => $accessKey,
            'secretKey' => $secretKey,
            'signature' => $signature,
            "partnerCode" => $paymentStatus['partnerCode'],
            "orderId" => $paymentStatus['orderId'],
            "requestId" => $paymentStatus['requestId'],
            "extraData" => $paymentStatus['extraData'],
            "amount" => $paymentStatus['amount'],
            "transId" => $paymentStatus['transId'],
            "payType" => $paymentStatus['payType'],
            "resultCode" => $paymentStatus['resultCode'],
            "refundTrans" => $paymentStatus['refundTrans'],
            "message" => $paymentStatus['message'],
            "responseTime" => $paymentStatus['responseTime'],
            "lastUpdated" => $paymentStatus['lastUpdated'],
        ];
        if ($paymentStatus['resultCode'] == 0) {
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $successStatus));

            return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-completed/' . $findOrderId);
        } elseif ($paymentStatus['resultCode'] == 1006) {
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $pendingStatus));

            return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-cancel/' . $findOrderId);
        } else {
            Event::dispatch(new PaymentSuccessfullyEvent($order, $paymentData, $pendingStatus));

            return redirect()->to(env('FRONTEND_URL') . '/checkout/payment-error/' . $findOrderId);
        }
    }
}
