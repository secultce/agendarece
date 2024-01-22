<?php

namespace App\Events;

use App\Models\Solicitation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyResponsibles
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Solicitation $solicitation)
    {
        $this->solicitation = $solicitation;
    }
}
