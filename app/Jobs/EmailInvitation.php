<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\InvitationNotification;
use App\Models\User;

class EmailInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ir_number;
    protected $complainant_user_id;
    protected $respondent_user_id;
    protected $invites;
    protected $date;
    protected $time;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $complainant_user_id,
        string $respondent_user_id,
        string $date,
        string $time,
        array $invites
    ) {
        $this->ir_number = $ir_number;
        $this->invites = $invites;
        $this->complainant_user_id = $complainant_user_id;
        $this->respondent_user_id = $respondent_user_id;
        $this->date = $date;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->complainant_user_id) {
            $complainant = User::findOrFail($this->complainant_user_id);
            if ($complainant['email_address']) {
                Notification::route('mail', $complainant['email_address'])
                ->notify(new InvitationNotification($this->ir_number, $this->date, $this->time));
            }
        }

        if ($this->respondent_user_id) {
            $respondent = User::findOrFail($this->respondent_user_id);
            if ($respondent['email_address']) {
                Notification::route('mail', $respondent['email_address'])
                ->notify(new InvitationNotification($this->ir_number, $this->date, $this->time));
            }
        }

        if ($this->invites) {
            foreach ($this->invites as $inv_id) {
                $inv = User::findOrFail($inv_id);
                if ($inv['email_address']) {
                    Notification::route('mail', $inv['email_address'])
                    ->notify(new InvitationNotification($this->ir_number, $this->date, $this->time));
                }
            }
        }
    }
}
