<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\NoReplyRespondentNotification;
use App\Models\User;

class EmailNoReplyRespondent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ir_number;
    public $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $email
    ) {
        $this->ir_number = $ir_number;
        $this->email = $email;
    }

     /**
      * Execute the job.
      *
      * @return void
      */
    public function handle()
    {
        if ($this->email || $this->email != '') {
            $this->sendTo($this->email, $this->ir_number, "No Reply");
        }
    }

    private function sendTo($userEmail, $ir_number, $type_send)
    {
        if ($type_send == "No Reply") {
            $type_notification = new NoReplyRespondentNotification($ir_number);
        }

        Notification::route('mail', $userEmail)->notify($type_notification);
    }
}
