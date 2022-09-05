<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class NoReplyOthersNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $date;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @return void
     */
    public function __construct(string $ir_number, string $date)
    {
        $this->ir_number = $ir_number;
        $this->date = $date;
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
        $ir_number = STR_PAD($this->ir_number, 9, '0', STR_PAD_LEFT);

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Respondent No Reply)')
            ->line(
                'This is to inform you that IR:#'. $ir_number .' filed to one of your agents last  ('.
                Carbon::parse($this->date)->format("l, F d, Y") . ') is already past due for response.
                Please note that agent waived his/her right to be heard. Kindly check your EP for updates'
            );
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
