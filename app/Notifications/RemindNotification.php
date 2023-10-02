<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Programmation;

class RemindNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $programmation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Programmation $programmation)
    {
        $this->programmation = $programmation;
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
        $programmationPeriod = \Helper::formattedPeriod($this->programmation);
        $mailMessage   = (new MailMessage)->mailer('sendmail')->greeting("Olá {$notifiable->name},");

        $mailMessage->subject("Lembrete de Programação - {$this->programmation->title}");
        $mailMessage->line("Faltam {$this->programmation->remind_at} dia(s) para a programação <strong>{$this->programmation->title}</strong> com a categoria <strong>{$this->programmation->category->name}</strong> e que irá ocorrer em <strong>{$programmationPeriod}</strong> nos espaços:");

        foreach ($this->programmation->spaces->pluck('space') as $space) $mailMessage->line("* {$space->name}");

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
