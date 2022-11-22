<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNextEmailByScenarioCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaignScenario;

    public $contact;

    /**
     * @param $campaignScenario
     * @param $contact
     */
    public function __construct($campaignScenario, $contact)
    {
        $this->campaignScenario = $campaignScenario;
        $this->contact = $contact;
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
