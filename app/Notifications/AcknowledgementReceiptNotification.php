<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class AcknowledgementReceiptNotification extends Notification
{
    use Queueable;

    public $acknowledgement_id;
    public $employee_name;
    public $date_received;

    public function __construct(string $acknowledgement_id, string $employee_name, string $date_received)
    {
        $this->acknowledgement_id = $acknowledgement_id;
        $this->employee_name = $employee_name;
        $this->date_received = $date_received;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $link = 'acknowledgementreceipt/'.$this->acknowledgement_id;

        return (new MailMessage)
            ->greeting('Dear ' . $this->employee_name . ',')
            ->subject('Message from EP (Acknowledgement Receipt)')
            ->line(
                'Please be informed that an acknowledgement receipt dated '. Carbon::parse($this->date_received)->format("l, F d, Y") .' has been filed under your name.'
            )
            ->line(
                'Please confirm and acknowledge it by clicking this link.'
            )
            ->action('I Acknowledge', url($link));
    }
}
