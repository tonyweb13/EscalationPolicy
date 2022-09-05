<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\{
    Settings\Office,
    HrbpCluster,
    Respondent,
    Attachment,
    User
};
use PDF;
use DateTime;
use Carbon\Carbon;
use App\Jobs\EmailReplyRespondent;
use App\Http\Controllers\Api\Admin\IncidentReportController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class RespondentController extends Controller
{
    public function index() : JsonResponse
    {
        $complainant = Respondent::with(['respondent_user',
            'complainant', 'complainant.complainant_user',
            'complainant.offense', 'complainant.offense.category',
            'complainant.attachments', 'complainant.offense.gravity',
            'incident_report',
            // 'progression_offense.respondent_first_offense.complainant.offense',
            // 'progression_offense.respondent_second_offense.complainant.offense',
            // 'progression_offense.respondent_third_offense.complainant.offense',
            // 'progression_offense.respondent_fourth_offense.complainant.offense',
            // 'progression_offense.respondent_fifth_offense.complainant.offense',
            // 'progression_offense.respondent_sixth_offense.complainant.offense',
            // 'progression_offense.respondent_seventh_offense.complainant.offense',
            ])
            ->where('respondent_user_id', Auth()->id())
            ->where('status_id', '!=', 1)
            ->orderBy('ir_number', 'desc')
            ->get();

        return response()->json($complainant);
    }

    public function countIRReply()
    {
        $respondent = Respondent::with(['hrbp_user'])
        ->where('response', '')
        ->where('status_id', 2)
        ->where('respondent_user_id', Auth()->id())
        ->get();

        return $respondent->count();
    }

    public function countOnHoldReply()
    {
        $respondent = Respondent::with(['hrbp_user'])
        ->where('response', '')
        ->where('status_id', 3)
        ->where('respondent_user_id', Auth()->id())
        ->get();

        return $respondent->count();
    }

    public function countRespondent()
    {
        $respondentCount = $this->notificationRespondentReply()->count();
        return $respondentCount;
    }

    public function notificationRespondentReply()
    {
        $respondent = Respondent::with(['hrbp_user'])
        ->where('response', '')
        ->where('status_id', '!=', 1)
        ->where('status_id', '!=', 4)
        ->where('respondent_user_id', Auth()->id())
        ->get();

        return $respondent;
    }

    public function updateResponse(int $id, Request $request)
    {
        if ($request->second_response != null && ($request->response == '<p>Will not further explain</p>' ||
            $request->response == 'Will not further explain')) {
            $respondent = Respondent::where('id', $id)
            ->where('respondent_user_id', Auth()->id())
            ->update([
                'second_response' => $request->second_response,
                'second_response_date' => Carbon::now()
            ]);
        } else {
            $respondent = Respondent::where('id', $id)
            ->where('respondent_user_id', Auth()->id())
            ->update([
                'response' => $request->response,
                'date_response' => Carbon::now()
            ]);
        }
        if ($request->response) {
            $respondent_mail = Respondent::with([
                'hrbp_user',
                'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign',
            ])
                ->where('id', $id)
                ->where('respondent_user_id', Auth()->id())
                ->first();

            $hrbp = $respondent_mail->hrbp_user->email_address != null ?
                $respondent_mail->hrbp_user->email_address : '';
            $respondent = $respondent_mail->respondent_user->email_address != null ?
                $respondent_mail->respondent_user->email_address : '';
            $supervisor = ($respondent_mail->respondent_user->employee_supervisor != null &&
                $respondent_mail->respondent_user->employee_supervisor->supervisor_assign->email_address != null) ?
                $respondent_mail->respondent_user->employee_supervisor->supervisor_assign->email_address : '';

            $manager = ($respondent_mail->respondent_user->employee_supervisor
                ->supervisor_assign->employee_supervisor != null && $respondent_mail->respondent_user
                ->employee_supervisor->supervisor_assign->employee_supervisor->supervisor_assign
                ->email_address != null) ? $respondent_mail->respondent_user->employee_supervisor->supervisor_assign
                ->employee_supervisor->supervisor_assign->email_address : '';
            if ($respondent_mail->count() > 0) {
                if ($hrbp != '') {
                    IncidentReportController::replyEmail(
                        $respondent_mail->ir_number,
                        $hrbp,
                        $respondent_mail->date_response
                    );
                }
                if ($respondent != '') {
                    IncidentReportController::replyEmail(
                        $respondent_mail->ir_number,
                        $respondent,
                        $respondent_mail->date_response
                    );
                }
                if ($supervisor != '') {
                    IncidentReportController::replyEmail(
                        $respondent_mail->ir_number,
                        $supervisor,
                        $respondent_mail->date_response
                    );
                }
                if ($manager != '') {
                    IncidentReportController::replyEmail(
                        $respondent_mail->ir_number,
                        $manager,
                        $respondent_mail->date_response
                    );
                }
            }
        }

        if ($respondent) {
            $email_acknowledge =  Respondent::where('id', $id)
            ->where('email_acknowledge_date', null)
            ->where('respondent_user_id', Auth()->id())
            ->update([
                'email_acknowledge_date' => Carbon::now(),
            ]);

            if ($email_acknowledge) {
                $status = Respondent::where('id', $id)
                ->where('respondent_user_id', Auth()->id())
                ->first();

                IncidentReportController::compute_ageing($status);
            }
        }

        if (json_encode($request->attachments)) {
            $attach = new Attachment;
            $attach->complainant_id = $request->complainant_id;
            $attach->respondent_id = $id;
            $attach->attachments = json_encode($request->respondent_attachments);

            if ($attach->save()) {
                Respondent::where('id', $id)
                ->where('complainant_id', $request->complainant_id)
                ->update([
                    'respondent_attachments_id' => $attach->id,
                ]);
            }
        }

        return $respondent;
    }

    public function updateAcknowledgeResponse(int $ir_number)
    {
        $respondent = Respondent::where('ir_number', $ir_number)
        ->update([
            'hrbp_acknowledge_response' => 1,
            'hrbp_date_acknowledge' => Carbon::now(),
        ]);

        return $respondent;
    }

    public function updateAttend(int $id, Request $request)
    {
        $respondent = Respondent::where('id', $id)
            ->where('respondent_user_id', Auth()->id())
            ->where('status_id', '!=', 1)
            ->update(['attend' => $request->attend]);

        return $respondent;
    }

    public function getRespondentHr($id)
    {
        /* checking to get HRSL and HRBP */
        $getHrbp = User::with(['employee_supervisor.hr_assign'])->findOrFail($id);
        return $getHrbp;
    }

    public function acknowledgementLetter($respondent_user_id)
    {
        $respondent = Respondent::with([
            'respondent_user',
            'respondent_user.department',
            'respondent_user.employee_supervisor.supervisor_assign.designation',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
            'respondent_user.designation',
            'complainant.offense',
            'incident_report.hrbp_user',
            'incident_report.hrbp_user.designation',
        ])
        ->where('respondent_user_id', $respondent_user_id)
        ->first();
        if ($respondent->start_date) {
            $start_date_time = new dateTime($respondent->start_date);
            $start_date = $start_date_time->format("F d, Y");
        } else {
            $start_date =  '<Insert Start Date>';
        }
        if ($respondent->takes_date != null && $respondent->start_date != null) {
            $takes_date_time = new dateTime($respondent->takes_date);
            $takes_date = $takes_date_time->format("F d, Y");
        } elseif ($respondent->takes_date == null && $respondent->start_date != null) {
            $takes_date = 'Immediately';
        }

        if ($respondent->last_date != null && $respondent->start_date != null) {
            $last_date_time = new dateTime($respondent->last_date);
            $last_date = $last_date_time->format("F d, Y");
        } elseif ($respondent->last_date == null && $respondent->start_date != null) {
            $last_date = 'Immediately';
        }

        $date_start = $respondent->complainant->date_incident_start ?
            $respondent->complainant->date_incident_start : "";
        $date_end = $respondent->complainant->date_incident_end ? $respondent->complainant->date_incident_end : "";
        $data = [
            'start_date' => $start_date,
            'takes_date' => ($takes_date) ? $takes_date : '<Insert Takes Date>',
            'last_date' => ($last_date) ? $last_date : '<Insert Last Date>',
            'ir_number' => $respondent->ir_number,
            'date_transpired' => $date_start . " - " . $date_end,
            'offense' => $respondent->complainant->offense->offense,
            'supervisor_name' => $respondent->respondent_user->employee_supervisor->supervisor_assign->first_name
            . " " . $respondent->respondent_user->employee_supervisor->supervisor_assign->last_name,
            'supervisor_emp_id' => $respondent->respondent_user->employee_supervisor->supervisor_assign->employee_no,
            'supervisor_position' => $respondent->respondent_user->employee_supervisor->supervisor_assign
                ->designation->name,
            'res_full_name' => $respondent->respondent_user->first_name
            . " " . $respondent->respondent_user->last_name,
            'res_emp_no' => $respondent->respondent_user->employee_no,
            'res_position' => $respondent->respondent_user->designation->name,
            'res_department' => $respondent->respondent_user->department->name,
            'hrbp_full_name' => $respondent->incident_report->hrbp_user->first_name
            . " " . $respondent->incident_report->hrbp_user->last_name,
            'hrbp_emp_no' => $respondent->incident_report->hrbp_user->employee_no,
            'hrbp_position' => $respondent->incident_report->hrbp_user->designation->name,
            'manager_name' => $respondent->respondent_user->employee_supervisor->supervisor_assign->employee_supervisor
                ->supervisor_assign->first_name . " " . $respondent->respondent_user->employee_supervisor
                ->supervisor_assign->employee_supervisor->supervisor_assign->last_name,
            'manager_emp_no' => $respondent->respondent_user->employee_supervisor->supervisor_assign
                ->employee_supervisor->supervisor_assign->employee_no,
            'manager_possition' => $respondent->respondent_user->employee_supervisor->supervisor_assign
                ->employee_supervisor->supervisor_assign->designation->name,
        ];
        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false) {
            $office = Office::where('site', 'like', '%square%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $data['work_location'] = $office->complete_address;
        }

        $pdf = PDF::loadView('pdf.download.acknowledge_letter', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->download('HR Acknowledge Letter.pdf');
    }

    public function attachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/respondent/'
                . auth()->user()->employee_no
                . '/' . Carbon::today()->format('Y-m-d')
                . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'respondent/'
                    . auth()->user()->employee_no . '/'
                    . Carbon::today()->format('Y-m-d')
                    . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded]);
        }
    }

    public function downloadAttachment($folder, $emp_no, $date, $filename)
    {
        return response()->download(storage_path("app/public/attachments/{$folder}/{$emp_no}/{$date}/{$filename}"));
    }

    public function replyReactivate($ir_number)
    {
        Respondent::where('ir_number', $ir_number)
        ->update(['reply_reactivate' => Carbon::now()->addDays(2)->format("Y-m-d")]);
    }
}
