<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\ClosedIncidentReportNotification;
use App\Models\User;

class EmailClosedIncidentReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ir_number;
    protected $hr_user_id;
    protected $complainant_user_id;
    protected $respondent_user_id;
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
            $hrbp = User::findOrFail($this->hr_user_id);

            if ($hrbp['email_address']) {
                $hrbp_email_address = $hrbp['email_address'];
                Notification::route('mail', $hrbp_email_address)
                ->notify(new ClosedIncidentReportNotification($this->ir_number, $hrbp_email_address));
            }

            if ($this->complainant_user_id) {
                $complainant = User::findOrFail($this->complainant_user_id);

                if ($complainant['email_address']) {
                    Notification::route('mail', $complainant['email_address'])
                    ->notify(new ClosedIncidentReportNotification($this->ir_number, $hrbp_email_address));
                }
            }

            if ($this->respondent_user_id) {
                $respondent = User::findOrFail($this->respondent_user_id);

                if ($respondent['email_address']) {
                    Notification::route('mail', $respondent['email_address'])
                    ->notify(new ClosedIncidentReportNotification($this->ir_number, $hrbp_email_address));
                }
            }
        }
    }
}
