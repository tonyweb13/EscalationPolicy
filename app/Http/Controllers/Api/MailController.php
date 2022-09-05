<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use Notification;
use App\Models\{
    // HrbpCluster,
    User
};
use App\Notifications\{
    // NewIncidentReportNotification,
    // ClosedIncidentReportNotification,
    // ModifyIncidentReportNotification,
    // GeneratedNteNotification,
    // GeneratedIrrNotification,
    // NoteNotification,
    InvitationNotification
};

class MailController extends Controller
{
    // public function newIr($ir_number, $employee_no)
    // {
    //     $hrs = HrbpCluster::with(['teamlead_cluster'])->get();

    //     $sendHRM = 0;
    //     foreach ($hrs as $hr) {
    //         foreach ($hr['teamlead_cluster'] as $teamlead_cluster) {
    //             if ($teamlead_cluster['employee_no'] == $employee_no) {
    //                 if ($hr->hrsl_email_address) {
    //                     $this->sendTo($hr->hrsl_email_address, $ir_number, "NewIR");
    //                     $sendHRM = 1;
    //                 }

    //                 if ($hr->hrbp_email_address) {
    //                     $this->sendTo($hr->hrbp_email_address, $ir_number, "NewIR");
    //                 }
    //             }
    //         }
    //     }

    //     if ($sendHRM) {
    //         /* send to Senior Manager Human Capital*/
    //         $hrm = User::where('employee_no', 20160004)->first();
    //         $this->sendTo($hrm->email_address, $ir_number, "NewIR");
    //     }
    // }

    // public function closedIr($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id)
    // {
    //     if ($hr_user_id) {
    //         $hrbp = User::findOrFail($hr_user_id);
    //         $hrbp_email_address = $hrbp['email_address'];
    //         Notification::route('mail', $hrbp_email_address)
    //         ->notify(new ClosedIncidentReportNotification($ir_number, $hrbp_email_address));

    //         if ($complainant_user_id) {
    //             $complainant = User::findOrFail($complainant_user_id);
    //             Notification::route('mail', $complainant['email_address'])
    //             ->notify(new ClosedIncidentReportNotification($ir_number, $hrbp_email_address));
    //         }

    //         if ($respondent_user_id) {
    //             $respondent = User::findOrFail($respondent_user_id);
    //             Notification::route('mail', $respondent['email_address'])
    //             ->notify(new ClosedIncidentReportNotification($ir_number, $hrbp_email_address));
    //         }
    //     }
    // }

    // public function modifyIr(
    //     $ir_number,
    //     $hr_user_id,
    //     $complainant_user_id,
    //     $respondent_user_id,
    //     $invite_user_id,
    //     $changes
    // ) {
    //     if ($hr_user_id) {
    //         $hrbp = User::findOrFail($hr_user_id);
    //         $hrbp_email_address = $hrbp['email_address'];
    //         Notification::route('mail', $hrbp_email_address)
    //         ->notify(new ModifyIncidentReportNotification($ir_number, $changes));
    //     }

    //     if ($complainant_user_id) {
    //         $complainant = User::findOrFail($complainant_user_id);
    //         Notification::route('mail', $complainant['email_address'])
    //         ->notify(new ModifyIncidentReportNotification($ir_number, $changes));
    //     }

    //     if ($respondent_user_id) {
    //         $respondent = User::findOrFail($respondent_user_id);
    //         Notification::route('mail', $respondent['email_address'])
    //         ->notify(new ModifyIncidentReportNotification($ir_number, $changes));
    //     }

    //     if ($invite_user_id) {
    //         $invite = User::findOrFail($invite_user_id);
    //         Notification::route('mail', $invite['email_address'])
    //             ->notify(new ModifyIncidentReportNotification($ir_number, $changes));
    //     }

    //     /* insert HR invites */
    // }

    // public function noteComplainant($ir_number, $complainant_user_id)
    // {
    //     if ($complainant_user_id && $ir_number) {
    //         $complainant = User::findOrFail($complainant_user_id);
    //         Notification::route('mail', $complainant['email_address'])
    //         ->notify(new NoteNotification($ir_number));
    //     }
    // }

    // public function noteRespondent($ir_number, $respondent_user_id)
    // {
    //     if ($respondent_user_id && $ir_number) {
    //         $respondent = User::findOrFail($respondent_user_id);
    //         Notification::route('mail', $respondent['email_address'])
    //         ->notify(new NoteNotification($ir_number));
    //     }
    // }

    // public function noteHrbp($ir_number, $noted_by_user_id)
    // {
    //     if ($noted_by_user_id && $ir_number) {
    //         $noted_by = User::findOrFail($noted_by_user_id);
    //         Notification::route('mail', $noted_by['email_address'])
    //         ->notify(new NoteNotification($ir_number));
    //     }
    // }

    public function invitation($ir_number, $date, $time, $complainant_user_id, $respondent_user_id, $invites)
    {
        if ($complainant_user_id) {
            $complainant = User::findOrFail($complainant_user_id);
            Notification::route('mail', $complainant['email_address'])
            ->notify(new InvitationNotification($ir_number, $date, $time));
        }

        if ($respondent_user_id) {
            $respondent = User::findOrFail($respondent_user_id);
            Notification::route('mail', $respondent['email_address'])
            ->notify(new InvitationNotification($ir_number, $date, $time));
        }

        if ($invites) {
            $invite = explode(",", $invites);

            foreach ($invite as $inv_id) {
                $inv = User::findOrFail($inv_id);
                Notification::route('mail', $inv['email_address'])
                ->notify(new InvitationNotification($ir_number, $date, $time));
            }
        }
    }

    // public function generatedNte($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id)
    // {
    //     $this->generatedEmail($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id, "GeneratedNTE");
    // }

    // public function generatedIrr($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id)
    // {
    //     $this->generatedEmail($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id, "GeneratedIRR");
    // }

    // private function generatedEmail($ir_number, $hr_user_id, $complainant_user_id, $respondent_user_id, $type_send)
    // {
    //     if ($hr_user_id) {
    //         $hr = User::findOrFail($hr_user_id);
    //         $this->sendTo($hr['email_address'], $ir_number, $type_send);
    //     }

    //     if ($complainant_user_id) {
    //         $complainant = User::findOrFail($complainant_user_id);
    //         $this->sendTo($complainant['email_address'], $ir_number, $type_send);
    //     }

    //     if ($respondent_user_id) {
    //         $respondent = User::findOrFail($respondent_user_id);
    //         $this->sendTo($respondent['email_address'], $ir_number, $type_send);
    //     }
    // }

    // private function sendTo(string $userEmail, int $ir_number, $type_send)
    // {
    //     if ($type_send == "NewIR") {
    //         $type_notification = new NewIncidentReportNotification($ir_number);
    //     }

    //     if ($type_send == "GeneratedNTE") {
    //         $type_notification = new GeneratedNteNotification($ir_number);
    //     }

    //     if ($type_send == "GeneratedIRR") {
    //         $type_notification = new GeneratedIrrNotification($ir_number);
    //     }

    //     Notification::route('mail', $userEmail)->notify($type_notification);
    // }
}
