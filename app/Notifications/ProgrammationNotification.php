<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Programmation;

class ProgrammationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $action;
    public $programmation;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, string $action, Programmation $programmation = null, $data = null)
    {
        $this->user          = $user;
        $this->action        = $action;
        $this->programmation = $programmation;
        $this->data          = $data;
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
        $programmation = $this->programmation ?? $this->data;
        $period        = \Helper::formattedPeriod($programmation);
        $mailMessage   = (new MailMessage)->mailer('sendmail')->greeting("Olá {$notifiable->name},");

        switch ($this->action) {
            case 'created':
                $subjectPeriod = \Helper::formattedPeriod($programmation, "%b %d");

                $mailMessage->subject("Nova Programação para {$subjectPeriod}");
                $mailMessage->line("Criada por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> tem data prevista para ocorrer em <strong>{$period}</strong> nos espaços:");

                foreach ($programmation->spaces->pluck('space') as $space) $mailMessage->line("* {$space->name}");
            break;

            case 'destroyed':
                $mailMessage->subject("Programação {$programmation->title} removida");
                $mailMessage->line("Removida por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> que tinha data prevista para ocorrer em <strong>{$period}</strong> nos espaços:");

                foreach ($programmation->spaces as $space) $mailMessage->line("* {$space->name}");

                $mailMessage->line("Foi removida.");
            break;

            case 'title_updated':
                $mailMessage->subject("Mudança de título na programação {$this->data->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$this->data->title}</strong> teve seu título alterado para <strong>{$programmation->title}</strong>");
            break;

            case 'time_updated':
                $oldTime = \Helper::formattedTime($this->data->start_time, $this->data->end_time);
                $newTime = \Helper::formattedTime($programmation->start_time, $programmation->end_time);

                $mailMessage->subject("Mudança de horário na programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> teve seu horário alterado de <strong>{$oldTime}</strong> para <strong>{$newTime}</strong>");
            break;

            case 'date_updated':
                $oldDate = \Helper::formattedDate($this->data->start_date, $this->data->end_date, '%d/%m');
                $newDate = \Helper::formattedDate($programmation->start_date, $programmation->end_date, '%d/%m');

                $mailMessage->subject("Mudança na data da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> teve sua data alterada de <strong>{$oldDate}</strong> para <strong>{$newDate}</strong>");
            break;

            case 'category_updated':
                $mailMessage->subject("Mudança na categoria da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve sua categoria alterada de <strong>{$this->data->category->name}</strong> para <strong>{$programmation->category->name}</strong>");
            break;

            case 'parental_rating_updated':
                $mailMessage->subject("Mudança na classificação indicativa da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve sua classificação indicativa alterada de <strong>{$this->data->parental_rating_alias}</strong> para <strong>{$programmation->parental_rating_alias}</strong>");
            break;

            case 'spaces_updated':
                $oldSpaces = $this->data->spaces;
                $newSpaces = $programmation->spaces->pluck('space');

                $mailMessage->subject("Mudança nos espaços da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve seus espaços alterados de:");

                foreach ($oldSpaces as $oldSpace) $mailMessage->line("* {$oldSpace->name}");

                $mailMessage->line("para:");

                foreach ($newSpaces as $newSpace) $mailMessage->line("* {$newSpace->name}");
            break;

            case 'users_updated':
                $oldUsers = $this->data->users;
                $newUsers = $programmation->users->pluck('user');

                $mailMessage->subject("Mudança nos responsáveis pela programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve seus responsáveis alterados de:");

                foreach ($oldUsers as $oldUser) $mailMessage->line("* {$oldUser->name}");

                $mailMessage->line("para:");

                foreach ($newUsers as $newUser) $mailMessage->line("* {$newUser->name}");
            break;

            case 'users_removed':
                $mailMessage->subject("Removido da programação {$programmation->title}");
                $mailMessage->line("Removido por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("Você foi removido da programação <strong>{$programmation->title}</strong>");
            break;

            case 'link_created':
                $mailMessage->subject("Novo link para programação {$programmation->title}");
                $mailMessage->line("Novo link criado por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->action("Acessar " . $this->data->name, $this->data->link);
            break;

            case 'link_updated':
                $mailMessage->subject("Link atualizado na programação {$programmation->title}");
                $mailMessage->line("Link atualizado por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->action("Acessar " . $this->data->name, $this->data->link);
            break;

            case 'link_destroyed':
                $mailMessage->subject("Link removido da programação {$programmation->title}");
                $mailMessage->line("Link <strong>{$this->data}</strong> removido por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
            break;

            case 'note_created':
                $mailMessage->subject("Nova nota para programação {$programmation->title}");
                $mailMessage->line("Nova nota criada por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line($this->data);
            break;

            case 'note_updated':
                $mailMessage->subject("Nota atualizada na programação {$programmation->title}");
                $mailMessage->line("Nota atualizada por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line($this->data);
            break;

            case 'note_destroyed':
                $mailMessage->subject("Nota removida da programação {$programmation->title}");
                $mailMessage->line("Nota removida por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
            break;

            case 'comment_created':
                $mailMessage->subject("Novo comentário na programação {$programmation->title}");
                $mailMessage->line("Comentário criado por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line($this->data);
            break;

            case 'comment_updated':
                $mailMessage->subject("Comentário atualizado na programação {$programmation->title}");
                $mailMessage->line("Comentário atualizado por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line($this->data);
            break;

            case 'comment_destroyed':
                $mailMessage->subject("Comentário removido da programação {$programmation->title}");
                $mailMessage->line("Comentário removido por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
            break;

            case 'accessibilities_updated':
                $mailMessage->subject("Opções de acessibilidade atualizada da Programação {$programmation->title}");
                $mailMessage->line("As opções de acessibilidade foram alteradas por <strong>{$this->user->name}</strong> na programação <strong>{$programmation->title}</strong> da agenda <strong>{$programmation->schedule->name}</strong>");
            break;
        }

        return $mailMessage;
    }
}
