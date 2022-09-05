<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class GeneratedNteNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $reported;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @return void
     */
    public function __construct(string $ir_number, string $reported)
    {
        $this->ir_number = $ir_number;
        $this->reported = $reported;
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
        $link = 'acknowledge/'.$this->ir_number;

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Generate NTE)')
            ->line(
                'This is to inform you that an incident report ' . $ir_number . '
                was submitted last '. Carbon::parse($this->reported)->format("l, F d, Y") .' and is already validated.
                You are hereby required to explain within five (5) calendar days from receipt of this memorandum why no
                behavioral action including but not limited to the penalty of dismissal from employment, should be
                imposed upon you based on the following ground.'
            )
            ->line(
                'Failure on your part to answer the charges against you within the time we have given you will mean a
                waiver of the right to be heard, and we shall resolve the matter based on the evidence on record.
                Management will then evaluate on the case on hand based on the facts and advise you accordingly on the
                final resolution of this case.'
            )
            ->line('Please check your EP for further details/instruction')
            ->action('I Acknowledge', url($link));
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
