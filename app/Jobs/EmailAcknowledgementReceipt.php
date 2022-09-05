<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\AcknowledgementReceiptNotification;

class EmailAcknowledgementReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $acknowledgement_id;
    public $employee_name;
    public $email_address;
    public $date_received;

    public function __construct(
        string $acknowledgement_id,
        string $employee_name,
        string $email_address,
        string $date_received
    ) {
        $this->acknowledgement_id = $acknowledgement_id;
        $this->employee_name = $employee_name;
        $this->email_address = $email_address;
        $this->date_received = $date_received;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->email_address) {
            Notification::route('mail', $this->email_address)
            ->notify(new AcknowledgementReceiptNotification($this->acknowledgement_id, $this->employee_name, $this->date_received));
        }
    }
}
