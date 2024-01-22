<?php

namespace App\Providers;

use App\Events\NotifyResponsibles;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NotifyUsers;
use App\Events\RemindUsers;
use App\Listeners\SendProgrammationNotification;
use App\Listeners\SendProgrammationReminderNotification;
use App\Listeners\SendSolicitationNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NotifyUsers::class => [
            SendProgrammationNotification::class,
        ],
        RemindUsers::class => [
            SendProgrammationReminderNotification::class,
        ],
        NotifyResponsibles::class => [
            SendSolicitationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
