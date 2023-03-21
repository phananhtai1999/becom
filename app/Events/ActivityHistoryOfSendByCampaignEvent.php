<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Boolean;

class ActivityHistoryOfSendByCampaignEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var
     */
    public $model;

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $contact;

    /**
     * @param $model
     * @param $type
     * @param $contact
     */
    public function __construct($model, $type, $contact)
    {
        $this->model = $model;
        $this->type = $type;
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
