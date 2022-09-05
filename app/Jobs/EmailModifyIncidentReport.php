<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\ModifyIncidentReportNotification;
use App\Models\User;

class EmailModifyIncidentReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ir_number;
    protected $hr_user_id;
    protected $complainant_user_id;
    protected $respondent_user_id;
    protected $witness_user_id;
    protected $changes;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $hr_user_id,
        string $complainant_user_id,
        string $respondent_user_id,
        array $witness_user_id,
        array $changes
    ) {
        $this->ir_number = $ir_number;
        $this->hr_user_id = $hr_user_id;
        $this->complainant_user_id = $complainant_user_id;
        $this->respondent_user_id = $respondent_user_id;
        $this->witness_user_id = $witness_user_id;
        $this->changes = $changes;
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
                ->notify(new ModifyIncidentReportNotification($this->ir_number, $this->changes));
            }
        }

        if ($this->complainant_user_id) {
            $complainant = User::findOrFail($this->complainant_user_id);
            if ($complainant['email_address']) {
                Notification::route('mail', $complainant['email_address'])
                ->notify(new ModifyIncidentReportNotification($this->ir_number, $this->changes));
            }
        }

        if ($this->respondent_user_id) {
            $respondent = User::findOrFail($this->respondent_user_id);
            if ($respondent['email_address']) {
                Notification::route('mail', $respondent['email_address'])
                ->notify(new ModifyIncidentReportNotification($this->ir_number, $this->changes));
            }
        }

        if ($this->witness_user_id) {
            $witness = User::findOrFail($this->witness_user_id);
            if ($witness['email_address']) {
                Notification::route('mail', $witness['email_address'])
                ->notify(new ModifyIncidentReportNotification($this->ir_number, $this->changes));
            }
        }
    }
}
