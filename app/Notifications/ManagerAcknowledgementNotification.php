<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class ManagerAcknowledgementNotification extends Notification
{
    use Queueable;

    public $ir_id;
    public $ir_number;
    public $manager_name;
    public $employee_name;
    public $suspension_date;

    public function __construct(string $ir_id, string $ir_number, string $manager_name, string $employee_name, string $suspension_date)
    {
        $this->ir_id = $ir_id;
        $this->ir_number = $ir_number;
        $this->manager_name = $manager_name;
        $this->employee_name = $employee_name;
        $this->suspension_date = $suspension_date;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $link = 'acknowledgementmanager/'.$this->ir_id."/".$this->ir_number;
        $ir_number = '#' . STR_PAD($this->ir_number, 9, '0', STR_PAD_LEFT);

        return (new MailMessage)
            ->greeting('Dear ' . $this->manager_name . ',')
            ->subject('Message from EP (Manager Acknowledgement)')
            ->line(
                'Please be informed that suspension date/s for ' . $this->employee_name . ' with Incident Report '
                . $ir_number . ' on '. $this->suspension_date .
                ' has been plotted.'
            )
            ->line(
                'Please confirm and acknowledge it by clicking this link below.'
            )
            ->action('I Acknowledge', url($link));
    }
}
