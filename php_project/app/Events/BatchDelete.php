<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BatchDelete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $params = [];
    public $subject_type_id = 0;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($params, $subject_type_id)
    {
        //
        $this->params = $params;
        $this->subject_type_id = $subject_type_id;
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
