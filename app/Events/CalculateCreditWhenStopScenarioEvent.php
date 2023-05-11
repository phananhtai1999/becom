<?php

namespace App\Events;

use App\Models\Scenario;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalculateCreditWhenStopScenarioEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $scenario;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Scenario $scenario)
    {
        $this->scenario = $scenario;
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
