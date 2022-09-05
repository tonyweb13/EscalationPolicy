<?php
declare (strict_types = 1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ModifyIncidentReportNotification extends Notification
{
    use Queueable;

    public $ir_number;
    public $changes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $ir_number, array $changes)
    {
        $this->ir_number = $ir_number;

        /* {Note, Witness, Remark, Attachment, Status, Date of Admin Hearing} */
        $this->changes = $changes;
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
                    ->subject('Message from EP (Modify IR)')
                    ->line('Your Incident Report ' . $ir_number . ' has been updated by your respective
                        Human Resource Business Partner with a new ' . implode(" ", $this->changes) . '. The
                        turnaround time for processing Minor, Major & Serious Incident Reports is 12
                        days from the date the Incident Report was submitted by the complainant; Grave is 16 days
                        from the date the Incident Report was received by HR BP.')
                    ->line('Depending on the case , processing of Incident Reports may take more than the set
                        turnaround time. For follow ups and additional notes, please leave a note to your HR
                        Business Partner using the Note option.');
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
