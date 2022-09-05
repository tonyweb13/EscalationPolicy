<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class GeneratedIrrNotification extends Notification
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
            ->subject('Message from EP (Generate Case Closure)')
            ->line(
                'Case Closure letter has been generated for Incident Report ' . $ir_number . '.
                For follow ups and additional notes, please leave a note to your HR Business Partner using the
                Note option.'
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
