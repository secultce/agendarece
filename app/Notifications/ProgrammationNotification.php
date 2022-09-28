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
        $period        = $this->formattedPeriod($programmation);
        $mailMessage   = (new MailMessage)->greeting("Olá {$notifiable->name},");

        switch ($this->action) {
            case 'created':
                $mailMessage->subject("Nova Programação para {$period}");
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
                $mailMessage->subject("Mudança de título na programação {$this->oldData->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$this->oldData->title}</strong> teve seu título alterado para <strong>{$programmation->title}</strong>");
            break;

            case 'time_updated':
                $oldTime = $this->formattedTime($this->oldData->start_time, $this->oldData->end_time);
                $newTime = $this->formattedTime($programmation->start_time, $programmation->end_time);

                $mailMessage->subject("Mudança de horário na programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> teve seu horário alterado de <strong>{$oldTime}</strong> para <strong>{$newTime}</strong>");
            break;

            case 'date_updated':
                $oldDate = $this->formattedDate($this->oldData->start_date, $this->oldData->end_date, '%d/%m');
                $newDate = $this->formattedDate($programmation->start_date, $programmation->end_date, '%d/%m');

                $mailMessage->subject("Mudança na data da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> com a categoria <strong>{$programmation->category->name}</strong> teve sua data alterada de <strong>{$oldDate}</strong> para <strong>{$newDate}</strong>");
            break;

            case 'category_updated':
                $mailMessage->subject("Mudança na categoria da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve sua categoria alterada de <strong>{$this->oldData->category->name}</strong> para <strong>{$programmation->category->name}</strong>");
            break;

            case 'spaces_updated':
                $oldSpaces = $this->oldData->spaces;
                $newSpaces = $programmation->spaces->pluck('space');

                $mailMessage->subject("Mudança nos espaços da programação {$programmation->title}");
                $mailMessage->line("Mudado por <strong>{$this->user->name}</strong> na agenda <strong>{$programmation->schedule->name}</strong>");
                $mailMessage->line("A programação <strong>{$programmation->title}</strong> teve seus espaços alterados de:");

                foreach ($oldSpaces as $oldSpace) $mailMessage->line("* {$oldSpace->name}");

                $mailMessage->line("para:");

                foreach ($newSpaces as $newSpace) $mailMessage->line("* {$newSpace->name}");
            break;

            case 'users_updated':
                $oldUsers = $this->oldData->users;
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
        }

        return $mailMessage;
    }

    private function formattedDate($startDate, $endDate, $format = "%B %d")
    {
        $auxStartDate = ucfirst(Carbon::parse($startDate)->formatLocalized($format));
        $auxEndDate   = $endDate ? ucfirst(Carbon::parse($endDate)->formatLocalized($format)) : 'Indefinido';

        return "{$auxStartDate} a {$auxEndDate}";
    }

    private function formattedTime($startTime, $endTime)
    {
        $startTime = Carbon::parse($startTime)->format('H:i');
        $endTime   = Carbon::parse($endTime)->format('H:i');

        return "{$startTime} as {$endTime}";
    }

    private function formattedPeriod($programmation)
    {
        $startDate = ucfirst(Carbon::parse($programmation->start_date)->formatLocalized('%B %d'));
        $startTime = Carbon::parse($programmation->start_time)->format('H:i');
        $endDate   = $programmation->end_date && $programmation->end_date > $programmation->start_date ? ucfirst(Carbon::parse($programmation->end_date)->formatLocalized('%B %d')) : 'Indefinido';
        $endTime   = Carbon::parse($programmation->end_time)->format('H:i');

        return "{$startDate}, {$startTime} até {$endDate}, {$endTime}";
    }
}
