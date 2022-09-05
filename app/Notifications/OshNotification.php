<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OshNotification extends Notification
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
        return (new MailMessage)
            ->greeting('Dear ' . $this->employee_name . ',')
            ->subject('Message from EP (OSH Module)')
            ->line('Thank you for finishing the Occupational Safety and Health Module!');
    }
}
