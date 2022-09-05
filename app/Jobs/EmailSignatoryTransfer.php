<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\PreparedBySignatoryNotification;

class EmailSignatoryTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ir_number;
    public $email_address;
    public $designation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $email_address,
        string $designation
    ) {
        $this->ir_number = $ir_number;
        $this->email_address = $email_address;
        $this->designation = $designation;
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
            ->notify(new PreparedBySignatoryNotification($this->ir_number, $this->designation));
        }
    }
}
