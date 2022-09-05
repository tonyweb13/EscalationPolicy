<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\{
    GeneratedNteNotification,
    ManagerNotification
};

class EmailGeneratedNte implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ir_number;
    public $respondent_email;
    public $supervisor_email;
    public $manager_email;
    public $reported;
    public $respo_name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $respondent_email,
        string $supervisor_email,
        string $manager_email,
        string $reported,
        string $respo_name
    ) {
        $this->ir_number = $ir_number;
        $this->respondent_email = $respondent_email;
        $this->supervisor_email = $supervisor_email;
        $this->manager_email = $manager_email;
        $this->reported = $reported;
        $this->respo_name = $respo_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->respondent_email) {
            Notification::route('mail', $this->respondent_email)
            ->notify(new GeneratedNteNotification($this->ir_number, $this->reported));
        }

        if ($this->supervisor_email) {
            Notification::route('mail', $this->supervisor_email)
            ->notify(new ManagerNotification($this->ir_number, $this->reported, $this->respo_name));
        }

        if ($this->manager_email) {
            Notification::route('mail', $this->manager_email)
            ->notify(new ManagerNotification($this->ir_number, $this->reported, $this->respo_name));
        }
    }
}
