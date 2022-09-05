<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NoteNotification extends Notification
{
    use Queueable;

    public $ir_number;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @return void
     */
    public function __construct(string $ir_number)
    {
        $this->ir_number = $ir_number;
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

        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Message from EP (Note)')
            ->line('Your Incident Report ' . $ir_number . ' has been updated by ')
            ->line('your respective Human Resource Business Partner with a new Note.')
            ->line('The turnaround time for processing Minor, Major & Serious Incident Reports is 12 days ')
            ->line('from the date the Incident Report was received by HR BP; Grave is 16 days from the date ')
            ->line('the Incident Report was received by HR BP. Depending on the case , processing of Incident ')
            ->line('Reports may take more than the set turnaround time. For follow ups and additional notes, ')
            ->line('please leave a note to your HR Business Partner using the Note option.');
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
