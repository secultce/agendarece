<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Programmation;
use Carbon\Carbon;

class ProgrammationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $action;
    public $programmation;
    public $oldData;

    /**
     * Create a new notification instance.
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

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return $this->buildMailMessage($notifiable);
    }

    private function buildMailMessage($notifiable)
    {
        $programmation = $this->programmation ?? $this->oldData;
        $spaces        = $this->programmation ? $this->programmation->spaces->pluck('space') : $programmation->spaces;
        $users         = $this->programmation ? $this->programmation->users->pluck('space') : $programmation->users;
        $period        = $this->formattedPeriod($programmation);
        $mailMessage   = (new MailMessage)->greeting("Olá {$notifiable->name},");

        switch ($this->action) {
            case 'created':
                $mailMessage->subject("Nova Programação para {$period}");
                $mailMessage->line("Confira abaixo os detalhes desta nova programação");
                $mailMessage->line("Criada por <strong>{$this->user->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> tem data prevista para ocorrer em <strong>{$period}</strong> nos espaços:");

                foreach ($spaces as $space) $mailMessage->line("* {$space->name}");
            break;

            case 'destroyed':
                $mailMessage->subject("Programação {$programmation->title} removida");
                $mailMessage->line("Removida por <strong>{$this->user->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> que tinha data prevista para ocorrer em <strong>{$period}</strong> nos espaços:");

                foreach ($spaces as $space) $mailMessage->line("* {$space->name}");

                $mailMessage->line("Foi removida.");
            break;

            case 'time_updated':

            break;

            case 'date_updated':

            break;

            case 'category_updated':

            break;

            case 'spaces_updated':

            break;

            case 'users_updated':

            break;

            case 'users_removed':

            break;
        }

        return $mailMessage;
    }

    private function formattedPeriod($programmation)
    {
        $startDate = ucfirst(Carbon::parse($programmation->start_date)->formatLocalized('%B %d'));
        $startTime = Carbon::parse($programmation->start_time)->format('H:i');
        $endDate   = $programmation->end_date && $programmation->end_date > $programmation->start_date ? ucfirst(Carbon::parse($programmation->end_date)->formatLocalized('%B %d')) : '';
        $endTime   = Carbon::parse($programmation->end_time)->format('H:i');

        return "{$startDate}, {$startTime} - " . ($endDate ? "{$endDate}, " : '') . "{$endTime}";
    }
}
