<?php

namespace App\Listeners;

use App\Events\NotifyResponsibles;
use App\Notifications\SolicitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendSolicitationNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotifyResponsibles  $event
     * @return void
     */
    public function handle(NotifyResponsibles $event)
    {
        $users = $event->solicitation->schedule->users;

        $users->push($event->solicitation->schedule->user);

        Notification::send($users, (new SolicitationNotification('created', $event->solicitation))
            ->onConnection('database')
            ->onQueue('notifications')
        );
    }
}
