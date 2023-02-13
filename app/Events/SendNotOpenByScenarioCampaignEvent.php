<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotOpenByScenarioCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contactNotOpenByCampaignScenario;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contactNotOpenByCampaignScenario)
    {
        $this->contactNotOpenByCampaignScenario = $contactNotOpenByCampaignScenario;
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
