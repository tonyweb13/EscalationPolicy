<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\ManagerAcknowledgementNotification;

class EmailManagerAcknowledgement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ir_id;
    public $ir_number;
    public $manager_name;
    public $employee_name;
    public $manager_email;
    public $suspension_date;

    public function __construct(
        string $ir_id,
        string $ir_number,
        string $manager_name,
        string $employee_name,
        string $manager_email,
        string $suspension_date
    ) {
        $this->ir_id = $ir_id;
        $this->ir_number = $ir_number;
        $this->manager_name = $manager_name;
        $this->employee_name = $employee_name;
        $this->manager_email = $manager_email;
        $this->suspension_date = $suspension_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->manager_email) {
            Notification::route('mail', $this->manager_email)
            ->notify(new ManagerAcknowledgementNotification(
                $this->ir_id, $this->ir_number, $this->manager_name, 
                $this->employee_name, $this->suspension_date
            ));
        }
    }
}
