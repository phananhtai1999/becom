<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationSystemEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $type;

    public $action;

    public $model;

    /**
     * @param $user
     * @param $type
     * @param $action
     * @param $model
     */
    public function __construct($user, $type, $action, $model = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->action = $action;
        $this->model = $model;
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
