<?php

namespace App\Listeners;

use App\Events\NotifyUsers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProgrammationNotification;

class SendProgrammationNotification
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
     * @param  NotifyUsers  $event
     * @return void
     */
    public function handle(NotifyUsers $event)
    {
        $delay = now()->addMinutes(1);
        $users = ($event->programmation ? $event->programmation->users()->with('user')->get()->pluck('user') : $event->oldData->users)->filter(function ($user) use ($event) {
            return $user->id !== $event->user->id;
        });

        if ($event->action === 'users_updated') {
            $removedUsers = $event->oldData->users->diff($users);

            if (!$removedUsers->isEmpty()) Notification::send($removedUsers, (new ProgrammationNotification($event->user, "users_removed", $event->programmation, $event->oldData))
                ->delay($delay)
                ->onConnection('database')
                ->onQueue('notifications')
            );
        }

        if ($users->isEmpty()) return;
        
        Notification::send($users, (new ProgrammationNotification($event->user, $event->action, $event->programmation, $event->oldData))
            ->delay($delay)
            ->onConnection('database')
            ->onQueue('notifications')
        );
    }
}
