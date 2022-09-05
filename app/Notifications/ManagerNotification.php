<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class ManagerNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $reported;
    public $respo_name;

    /**
     * Create a notification instance.
     *
     * @param  string  $ir_number
     * @return void
     */
    public function __construct(string $ir_number, string $reported, string $respo_name)
    {
        $this->ir_number = $ir_number;
        $this->reported = $reported;
        $this->respo_name = $respo_name;
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
                    ->subject('Message from EP (NTE Notification)')
                    ->line('This is to inform you that incident report for ' . $this->respo_name . ' IR#:' . $ir_number
                    . ' submitted last ' . Carbon::parse($this->reported)->format("l, F d, Y"). ' has been validated
                    and is ready for routing. Failure to acknowledge within 24-48 hours will result to the cancellation
                    of Incident Report and may be subject for Disciplinary Action also, please take note that to be able
                    proceed with the creation of the Notice to Explain Memo. ')
                    ->line('Please check your EP for further details/instruction')
                    ->line('Thank You');
    }
}
