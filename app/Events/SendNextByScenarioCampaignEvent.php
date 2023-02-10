<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\CampaignScenario;
use App\Models\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNextByScenarioCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CampaignScenario
     */
    public $campaignScenario;

    /**
     * @var Contact
     */
    public $contact;

    /**
     * @var Campaign
     */
    public $campaign;

    /**
     * @param $campaign
     * @param $contact
     * @param $campaignScenario
     */
    public function __construct($campaign, $contact, $campaignScenario)
    {
        $this->campaignScenario = $campaignScenario;
        $this->contact = $contact;
        $this->campaign = $campaign;
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
