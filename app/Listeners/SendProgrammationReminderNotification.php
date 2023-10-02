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
        foreach ($event->programmations as $programmation) {
            $users = $programmation->users()->with('user')->get()->pluck('user');
    
            $users->push($programmation->user);
    
            Notification::send($users, (new RemindNotification($programmation))
                ->onConnection('database')
                ->onQueue('notifications')
            );
    
            $programmation->already_reminded = true;
            $programmation->save();
        }
    }
}
