<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class InvitationNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $date;
    public $time;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @param  string  $date
     * @param  string  $time
     * @return void
     */
    public function __construct(string $ir_number, string $date, string $time)
    {
        $this->ir_number = $ir_number;
        $this->date = $date;
        $this->time = $time;
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
        $date = $this->date;
        $time = $this->time;

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Invitation)')
            ->line(
                'An invitation has been sent to you to attend the Administrative Hearing on ' .
                Carbon::parse($date)->format("l, F d, Y") . ' ' . $time . ' for the Incident Report ' . $ir_number .
                '. Kindly acknowledge your attendance by logging in to your EP. '
            )
            ->line(
                'Failure on your part to acknowledge your attendance within 24-48hrs will mean a waiver of your right
                to be heard, and we shall resolve the matter based on the evidence on record. Management will then
                evaluate on the case on hand based on the facts and advise you accordingly on the final resolution of
                this case.'
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
