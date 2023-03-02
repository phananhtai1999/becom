<?php

namespace App\Events;

use App\Models\CreditPackage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCreditPackageSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $creditPackageUuid;
    public $paymentData;
    public $userUuid;
    public $paymentMethodUuid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        $creditPackageUuid,
        $paymentData,
        $userUuid,
        $paymentMethodUuid
    )
    {
        $this->creditPackageUuid = $creditPackageUuid;
        $this->paymentData = $paymentData;
        $this->userUuid = $userUuid;
        $this->paymentMethodUuid = $paymentMethodUuid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
