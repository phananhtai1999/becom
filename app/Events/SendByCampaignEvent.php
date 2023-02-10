<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Boolean;

class SendByCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Campaign
     */
    public $campaign;

    /**
     * @var int
     */
    public $creditNumberSendByCampaign;

    /**
     * @param $campaign
     * @param $creditNumberSendByCampaign
     */
    public function __construct($campaign, $creditNumberSendByCampaign)
    {
        $this->campaign = $campaign;
        $this->creditNumberSendByCampaign = $creditNumberSendByCampaign;
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
