<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notification;
use App\Notifications\NewIncidentReportNotification;
use App\Models\{
    User,
    HrbpCluster
};

class EmailNewIncidentReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ir_number;
    protected $employee_no;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $ir_number,
        string $employee_no
    ) {
        $this->ir_number = $ir_number;
        $this->employee_no = $employee_no;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hrs = HrbpCluster::with(['teamlead_cluster'])->get();

        $sendHRM = 0;
        foreach ($hrs as $hr) {
            foreach ($hr['teamlead_cluster'] as $teamlead_cluster) {
                /*
                * Employee Number required to have hrbp cluster and
                * user groups in order to run this condition
                */
                if ($teamlead_cluster['employee_no'] == $this->employee_no) {
                    if ($hr->hrsl_email_address) {
                        $this->sendTo($hr->hrsl_email_address, $this->ir_number, "NewIR");
                        $sendHRM = 1;
                    }

                    if ($hr->hrbp_email_address) {
                        $this->sendTo($hr->hrbp_email_address, $this->ir_number, "NewIR");
                    }
                }
            }
        }

        if ($sendHRM) {
            /* send to Manager's of HR
            * Senior Manager Human Capital
            * HR Recruitment Manager
            */
            $getEmployeeNos = User::whereHas('designation', function ($query) {
                $query->where('name', 'LIKE', 'Senior Manager Human Capital');
            })->get(['employee_no']);

            foreach ($getEmployeeNos as $getEmpNo) {
                $hrm = User::where('employee_no', $getEmpNo['employee_no'])->first();
                if ($hrm->email_address) {
                    $this->sendTo($hrm->email_address, $this->ir_number, "NewIR");
                }
            }
        }
    }

    private function sendTo(string $userEmail, string $ir_number, $type_send)
    {
        if ($type_send == "NewIR") {
            $type_notification = new NewIncidentReportNotification($ir_number);
        }

        Notification::route('mail', $userEmail)->notify($type_notification);
    }
}
