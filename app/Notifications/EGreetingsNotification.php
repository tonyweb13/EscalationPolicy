<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EGreetingsNotification extends Notification
{
    use Queueable;

    public $employee_name;

    public function __construct(string $employee_name)
    {
        $this->employee_name = $employee_name;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $link = '/egreetings';

        return (new MailMessage)
            ->greeting('Dear ' . $this->employee_name . ',')
            ->subject('Message from EP (E-Greetings)')
            ->line("It's Your Birthday! Sending you smiles for every moment of your special day.")
            ->line("Please click the link below to see your E-greetings from your ARB Family.")
            ->action('EP Login', url($link));
    }
}
