<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Boolean;

class ActiveStatusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var
     */
    public $campaign;

    /**
     * @var
     */
    public $email;

    /**
     * @param $campaign
     * @param $email
     */
    public function __construct($campaign, $email)
    {
        $this->campaign = $campaign;
        $this->email = $email;
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
