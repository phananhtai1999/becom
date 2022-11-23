<?php

namespace App\Listeners;

use App\Events\PaymentSuccessfullyEvent;
use App\Models\PaymentLog;
use App\Services\OrderService;
use App\Services\PaymentLogsService;

class PaymentSuccessfullyListener
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var PaymentLogsService
     */
    protected $paymentLogsService;

    /**
     * Create the event listener.
     *
     * @param OrderService $orderService
     * @param PaymentLogsService $paymentLogsService
     */
    public function __construct(
        OrderService $orderService,
        PaymentLogsService $paymentLogsService
    )
    {
        $this->orderService = $orderService;
        $this->paymentLogsService = $paymentLogsService;
    }

    /**
     * Handle the event.
     *
     * @param PaymentSuccessfullyEvent $event
     * @return void
     */
    public function handle(PaymentSuccessfullyEvent $event)
    {
        $order = $event->order;
        $paymentData = $event->paymentData;
        $status = $event->status;

        $this->paymentLogsService->create([
            'order_uuid' => $order->uuid,
            'logs' => $paymentData,
            'status' => $status
        ]);
        $this->orderService->update($order, [
            'status' => $status,
        ]);
    }
}
