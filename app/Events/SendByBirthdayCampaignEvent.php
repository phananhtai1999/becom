<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendByBirthdayCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listBirthdayCampaign;

    /**
     * @param $listBirthdayCampaign
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
