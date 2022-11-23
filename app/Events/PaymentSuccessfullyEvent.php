<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessfullyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var
     */
    public $paymentData;

    /**
     * @var
     */
    public $status;

    /**
     * @param Order $order
     * @param $paymentData
     * @param $status
     */
    public function __construct(
        Order $order,
        $paymentData,
        $status
    ) {
        $this->order = $order;
        $this->paymentData = $paymentData;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
