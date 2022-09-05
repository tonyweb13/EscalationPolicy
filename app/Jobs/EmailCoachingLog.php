<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\{CoachingLogNotification, CoachingLogResponseNotification};
use App\Models\User;

class EmailCoachingLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cl_number;
    protected $employee_address;
    protected $date;
    protected $status;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $cl_number,
        string $employee_address,
        string $date,
        string $status
    ) {
        $this->cl_number = $cl_number;
        $this->employee_address = $employee_address;
        $this->date = $date;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->status == 'coaching_response') {
            if ($this->employee_address) {
                $employee_email_address = $this->employee_address;
                Notification::route('mail', $employee_email_address)
                ->notify(new CoachingLogNotification($this->cl_number, $employee_email_address));
            }
        }

        if ($this->status == 'coaching_acknowledge') {
            if ($this->employee_address) {
                $employee_email_address = $this->employee_address;
                Notification::route('mail', $employee_email_address)
                ->notify(new CoachingLogResponseNotification($this->cl_number, $employee_email_address, $this->date));
            }
        }
    }
}
