<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ClosedIncidentReportNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $hrbp_email_address;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @return void
     */
    public function __construct(string $ir_number, string $hrbp_email_address)
    {
        $this->ir_number = $ir_number;
        $this->hrbp_email_address = $hrbp_email_address;
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
        $ir_number = '#' . STR_PAD($this->ir_number, 9, '0', STR_PAD_LEFT);
        $hrbp_email = $this->hrbp_email_address;

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Closed IR)')
            ->line('Your Incident Report ' . $ir_number . ' has been closed.
            Thank you for using EP . if you have any concerns, please send an email to ' . $hrbp_email);
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
