<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CoachingLogResponseNotification extends Notification
{
    use Queueable;

    public $cl_number;
    public $cl_date;
    public $employee_email_address;

    /**
     * Create a notification instance.
     *
     * @param  string  $cl_number
     * @return void
     */
    public function __construct(string $cl_number, string $employee_email_address, string $cl_date)
    {
        $this->cl_number = $cl_number;
        $this->cl_date = $cl_date;
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
     * @return \Illuminate\Noconsole.log(JSON.stringify())tifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $cl_number = '#' . STR_PAD($this->cl_number, 9, '0', STR_PAD_LEFT);
        $employee_email_address = $this->employee_email_address;
        $cl_date = explode(' ', $this->cl_date);

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Coaching Log)')
            ->line('This is to inform you that respondent with CL  '.$cl_number.
            ' acknowledged the coaching log dated '.$cl_date[0].' for your perusal. Please check your EP for further details.');
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
