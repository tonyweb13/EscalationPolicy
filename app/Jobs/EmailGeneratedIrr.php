<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\GeneratedIrrNotification;
use App\Models\User;

class EmailGeneratedIrr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ir_number;
    public $hr_user_id;
    public $complainant_user_id;
    public $respondent_user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $hr_user_id,
        string $complainant_user_id,
        string $respondent_user_id
    ) {
        $this->ir_number = $ir_number;
        $this->hr_user_id = $hr_user_id;
        $this->complainant_user_id = $complainant_user_id;
        $this->respondent_user_id = $respondent_user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->hr_user_id) {
            $hr = User::findOrFail($this->hr_user_id);
            if ($hr['email_address']) {
                $this->sendTo($hr['email_address'], $this->ir_number, "GeneratedIRR");
            }
        }

        if ($this->complainant_user_id) {
            $complainant = User::findOrFail($this->complainant_user_id);
            if ($complainant['email_address']) {
                $this->sendTo($complainant['email_address'], $this->ir_number, "GeneratedIRR");
            }
        }

        if ($this->respondent_user_id) {
            $respondent = User::findOrFail($this->respondent_user_id);
            if ($respondent['email_address']) {
                $this->sendTo($respondent['email_address'], $this->ir_number, "GeneratedIRR");
            }
        }
    }

    private function sendTo(string $userEmail, string $ir_number, $type_send)
    {
        if ($type_send == "GeneratedIRR") {
            $type_notification = new GeneratedIrrNotification($ir_number);
        }

        Notification::route('mail', $userEmail)->notify($type_notification);
    }
}
