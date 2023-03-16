<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Boolean;

class ActivityHistoryEvent
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
    public $action;

    /**
     * @param $model
     * @param $type
     * @param $action
     */
    public function __construct($model, $type, $action)
    {
        $this->model = $model;
        $this->type = $type;
        $this->action = $action;
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
