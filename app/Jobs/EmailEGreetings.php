<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\EGreetingsNotification;

class EmailEGreetings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $employee_name;
    public $email_address;

    public function __construct(
        string $employee_name,
        string $email_address
    ) {
        $this->employee_name = $employee_name;
        $this->email_address = $email_address;
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
            ->notify(new EGreetingsNotification($this->employee_name));
        }
    }
}
