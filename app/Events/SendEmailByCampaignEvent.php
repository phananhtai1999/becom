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
use phpDocumentor\Reflection\Types\Boolean;

class SendEmailByCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Campaign
     */
    public $campaign;

    /**
     * Create a new event instance.
     *
     * @param $campaign
     * @param $toEmails
     */
    public function __construct($campaign)
    {
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
