<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\CampaignScenario;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailByCampaginRootScenarioEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Campaign
     */
    public $campaign;

    /**
     * @var int
     */
    public $creditNumberSendEmail;

    /**
     * @var CampaignScenario
     */
    public $campaignRootScenario;

    /**
     * Create a new event instance.
     *
     * @param $campaign
     * @param $creditNumberSendEmail
     */
    public function __construct($campaign, $creditNumberSendEmail, $campaignRootScenario)
    {
        $this->campaign = $campaign;
        $this->creditNumberSendEmail = $creditNumberSendEmail;
        $this->campaignRootScenario = $campaignRootScenario;
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
