<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $description;
    public $subject;
    public $result;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($description, $subject = 'activities', $result = true)
    {
        $this->description = $description;
        $this->subject = $subject;
        $this->result = $result;
    }
}
