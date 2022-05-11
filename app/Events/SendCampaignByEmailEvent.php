<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\Email;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendCampaignByEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Campaign
     */
    public $campaign;

    /**
     * @var Email
     */
    public $emails;

    /**
     * @var $quantityEmailWasSentPerUser
     */
    public $quantityEmailWasSentPerUser;

    /**
     * Create a new event instance.
     *
     * @param $campaign
     * @param $emails
     * @param $quantityEmailWasSentPerUser
     */
    public function __construct($campaign, $emails, $quantityEmailWasSentPerUser)
    {
        $this->campaign = $campaign;
        $this->emails = $emails;
        $this->quantityEmailWasSentPerUser = $quantityEmailWasSentPerUser;
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
