<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailByBirthdayCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listBirthdayCampaign;

    /**
     * @param $contactsByBirthdayCampaigns
     */
    public function __construct($listBirthdayCampaign)
    {
        $this->listBirthdayCampaign = $listBirthdayCampaign;
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
