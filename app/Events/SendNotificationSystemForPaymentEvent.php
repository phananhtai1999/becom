<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationSystemForPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dataPayment;
    public $featurePayment;
    /**
     * @param $data
     */
    public function __construct($dataPayment, $featurePayment)
    {
        $this->dataPayment = $dataPayment;
        $this->featurePayment = $featurePayment;
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
