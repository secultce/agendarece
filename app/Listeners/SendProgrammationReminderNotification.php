<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\RemindUsers;
use App\Notifications\RemindNotification;
use Illuminate\Support\Facades\Notification;

class SendProgrammationReminderNotification
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
     * @param  object  $event
     * @return void
     */
    public function handle(RemindUsers $event)
    {
        $users = $event->programmation->users()->with('user')->get()->pluck('user');

        $users->push($event->programmation->user);

        Notification::send($users, (new RemindNotification($event->programmation))
            ->onConnection('database')
            ->onQueue('notifications')
        );

        $event->programmation->already_reminded = true;
        $event->programmation->save();
    }
}
