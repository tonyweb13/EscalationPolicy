<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CoachingLogNotification extends Notification
{
    use Queueable;

    public $cl_number;
    public $employee_email_address;

    /**
     * Create a notification instance.
     *
     * @param  string  $cl_number
     * @return void
     */
    public function __construct(string $cl_number, string $employee_email_address)
    {
        $this->cl_number = $cl_number;
        $this->employee_email_address = $employee_email_address;
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
        $cl_number = '#' . STR_PAD($this->cl_number, 9, '0', STR_PAD_LEFT);
        $employee_email_address = $this->employee_email_address;
        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Coaching Log)')
            ->line('Please be informed that coaching log has been generated with CL '.$cl_number.
            '. Log in to your EP account and make sure to acknowledge the coaching log.');
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
