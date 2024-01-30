<?php

namespace App\Notifications;

use App\Models\Solicitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SolicitationNotification extends Notification
{
    use Queueable;

    public $action;
    public $solicitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $action, Solicitation $solicitation)
    {
        $this->action = $action;
        $this->solicitation = $solicitation;
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
        $period      = \Helper::formattedPeriod($this->solicitation);
        $mailer      = $this->action === 'created' ? 'sendmail' : 'smtp';
        $mailMessage = (new MailMessage)->mailer($mailer)->greeting("Olá {$notifiable->name},");

        switch ($this->action) {
            case 'created':
                $subjectPeriod = \Helper::formattedPeriod($this->solicitation, "%b %d");

                $mailMessage->subject("Nova Solicitação de Programação para {$subjectPeriod}");
                $mailMessage->line("Criada por <strong>{$this->solicitation->user->name}</strong> na agenda <strong>{$this->solicitation->schedule->name}</strong>");
                $mailMessage->line("A solicitação de programação <strong>{$this->solicitation->title}</strong> com a categoria <strong>{$this->solicitation->category->name}</strong> com a data <strong>{$period}</strong> nos espaços:");

                foreach ($this->solicitation->spaces->pluck('space') as $space) $mailMessage->line("* {$space->name}");

                if ($this->solicitation->description) {
                    $mailMessage->line('Com a seguinte descrição:');
                    $mailMessage->line($this->solicitation->description);
                }

                $mailMessage->line("Espera por você <strong>(ou demais responsáveis)</strong> para que seja aprovada. Clique no botão abaixo para aprovação, ou desconsidere esse e-mail caso não aprove a solicitação.");
                $mailMessage->action("Aprovar Solicitação", URL::temporarySignedRoute('solicitation-approve', now()->addDays(10), ['solicitation' => $this->solicitation->id]));
            break;

            case 'approved':
                $subjectPeriod = \Helper::formattedPeriod($this->solicitation, "%b %d");

                $mailMessage->subject("Solicitação de Programação para {$subjectPeriod} foi aprovada");
                $mailMessage->line("A sua solicitação de programação <strong>{$this->solicitation->title}</strong> com a categoria <strong>{$this->solicitation->category->name}</strong> com a data <strong>{$period}</strong> nos espaços:");
                
                foreach ($this->solicitation->spaces->pluck('space') as $space) $mailMessage->line("* {$space->name}");

                $mailMessage->line("Foi aprovada e cadastrada na agenda <strong>{$this->solicitation->schedule->name}</strong>.");
            break;
        }

        return $mailMessage;
    }
}