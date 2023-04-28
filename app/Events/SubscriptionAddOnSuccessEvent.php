<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionAddOnSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscriptionHistoryData;
    public $userAddOnData;
    public $userUuid;
    public $invoiceData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        $userUuid,
        $subscriptionHistoryData,
        $userAddOnData,
        $invoice
    )
    {
        $this->userUuid = $userUuid;
        $this->subscriptionHistoryData = $subscriptionHistoryData;
        $this->userAddOnData = $userAddOnData;
        $this->invoice = $invoice;
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
