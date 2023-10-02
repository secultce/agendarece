<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Programmation;

class RemindUsers
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $programmation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Programmation $programmation)
    {
        $this->programmation = $programmation;
    }
}
