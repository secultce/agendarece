<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Programmation;

class NotifyUsers 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $action;
    public $programmation;
    public $oldData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, string $action, Programmation $programmation = null, $oldData = [])
    {
        $this->user          = $user;
        $this->action        = $action;
        $this->programmation = $programmation;
        $this->oldData       = $oldData;
    }
}
