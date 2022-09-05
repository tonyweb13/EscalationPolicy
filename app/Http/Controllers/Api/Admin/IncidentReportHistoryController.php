<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use App\Models\{
    Admin\Settings\Coc\IncidentReportResolution,
    NteDaClosedStatus,
    ProgressionOccurrence,
    Respondent,
    Complainant,
    SignedDocument,
    UsersGroup,
    User,
    ReopenRequest
};
use App\Jobs\EmailClosedIncidentReport;
use App\Models\Admin\IncidentReport;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Admin\IncidentReportController;
use App\Events\OpenClosedCasesCreated;

class IncidentReportHistoryController extends Controller
{
    public function getIrHistoryAll()
    {
        $designation = auth()->user()->designation->name;
        $user_emp_no = auth()->user()->employee_no;
        $user_id = auth()->user()->id;
        $roles = auth()->user()->roles[0]->name;

        $irhistory = Respondent::with([
            'complainant.complainant_user',
            'respondent_user',
            'respondent_user.designation',
            'respondent_user.department',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign',
            'complainant.witness_user.witness_fullname',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attendancepointssystem.attendancepoint',
            'complainant.attachments',
            'complainant.complainant_user',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.mom_attachment',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'respondent_hearing',
            'progression_occurence',
            'attached_hr',
            'reopen.requestor_name',
            'reopen.approver_name',
            'hrsl_user',
        ]);

        $irhistory = $irhistory->where('status_id', 4);

        /* 1. Super Admin */
        if ($roles == "Super Admin Access") {
            $irhistory = $irhistory
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } elseif ($roles == "HR Admin Access") { /* 2. HR Admin */
            $irhistory = $irhistory->where('hrsl_employee_no', $user_emp_no)
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } elseif ($roles == "HR Access") { /* 3. HR Access */
            $irhistory = $irhistory->where('hrbp_employee_no', $user_emp_no)
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1
                || Str::contains($designation, 'Senior Operation Manager') == 1) {
                $irhistory = $irhistory
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            } elseif (Str::contains($designation, 'Resolution Manager') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['id'];
                }
                array_push($supArray, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            } else {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['id'];
                }
                array_push($supArray2, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray2)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            }
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Supervisor') == 1
        ||  Str::contains($designation, 'Team Lead') == 1) {
            /* 4. Regular Supervisor - Sup*/
            if (Str::contains($designation, 'Senior Operation Supervisor') == 1) {
                $irhistory = $irhistory
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            } else {
                $userSups = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($userSups as $userSup) {
                    $supArray[] = $userSup['employee_assign']['id'];
                }
                array_push($supArray, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            }
        } else { /* 5. Regular User */
            $irhistory = $irhistory->where('respondent_user_id', auth()->id())
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }

        $data_return = [];
        $offense = IncidentReportController::getOffenseOnlyTrashed();

        // foreach ($irhistory as $value) {
        foreach ($irhistory->items() as $value) {
            if (empty($value->complainant->offense)) {
                $value['offense'] = $value->complainant->attendancepointssystem->type_infraction;
            } else {
                if (in_array($value->complainant->offense->id, $offense)) {
                      $value['archived'] = 'Offense Already Archived';
                    }
                    $value['offense'] = $value->complainant->offense->offense;
                }
            $irr_exist = $this->IncidentReportResolutionCheckIfExist($value->incident_report->irr_id);
            $value['irr_exist'] = $irr_exist;
            array_push($data_return, $value);
        }

        // $data_to_collection = Collection::make($data_return);
        // return response()->json($data_to_collection);
        // return $irhistory;

        return $irhistory;
    }

    private function IncidentReportResolutionCheckIfExist($irr_id)
    {
        $exist = IncidentReportResolution::withTrashed()->where('id', $irr_id)->first();
        if ($exist->deleted_at) {
            $irr_exist = 'Corrective Action Already archived';
        }

        return $irr_exist;
    }

    public function attachSigned(Request $request)
    {
        if ($request->nte_uploaded) {
            $nte = $request->nte_uploaded;
        } elseif ($request->file('pics1')) {
            $storage = 'public/attachments/signed/nte/';
            $uploaded = [];
            foreach ($request->file('pics1') as $file) {
                $nte = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $nte, 'public');

                $uploaded[] = 'signed/nte/' . $nte;
            }
            $contents = Storage::disk('local')->files($storage);
        }

        if ($request->file('pics2')) {
            $storage = 'public/attachments/signed/irr/';
            $uploaded = [];
            foreach ($request->file('pics2') as $file) {
                $irr = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $irr, 'public');

                $uploaded[] = 'signed/irr/' . $irr;
            }
            $contents = Storage::disk('local')->files($storage);
        } else {
            $irr = '';
        }

        if (!empty($request->nte_uploaded) && $irr) {
            $signed = SignedDocument::where('ir_id', $request->ir_id)
                ->where('ir_number', $request->ir_number)
                ->where('complainant_id', $request->complainant_id)
                ->update(['irr_upload' => $irr]);
        } elseif (empty($request->nte_uploaded)) {
            $signed = new SignedDocument;
            $signed->ir_id = $request->ir_id;
            $signed->ir_number = $request->ir_number;
            $signed->complainant_id = $request->complainant_id;
            $signed->nte_upload = $nte;
            $signed->irr_upload = $irr;
            $signed->save();
        }

        if ($irr || (!empty($request->nte_uploaded) && $irr)) {
            Respondent::where('ir_id', $request->ir_id)
                ->where('ir_number', $request->ir_number)
                ->where('complainant_id', $request->complainant_id)
                ->update(['status_id' => 4]); //Close Status

            /* if Status Closed and upload IRR signed, sent email */
            if ($request->ir_number && $request->hr_user_id
            && $request->complainant_user_id && $request->respondent_user_id) {
                EmailClosedIncidentReport::dispatch(
                    $request->ir_number,
                    $request->hr_user_id,
                    $request->complainant_user_id,
                    $request->respondent_user_id
                );
            }
        }

        /* getting callback for ProgressionOffenseController@employeeProgressionOffense */
        // $signed->respondent_user_id = $request->respondent_user_id;
        // $signed->respondent_id = $request->respondent_id;
        // $signed->prescriptive_period = $request->prescriptive_period;
        // $signed->irr_id = $request->irr_id;
        // $signed->offense_id = $request->offense_id;

        return $signed;
    }

    public function downloadNTE($filename)
    {
        return response()->download(storage_path("app/public/attachments/signed/nte/{$filename}"));
    }

    public function downloadIRR($filename)
    {
        return response()->download(storage_path("app/public/attachments/signed/irr/{$filename}"));
    }

    public function create(Request $request)
    {
        $reopen = new ReopenRequest;
        $reopen->ir_id = $request->ir_id;
        $reopen->respondent_id = $request->respondent_id;
        $reopen->ir_number = $request->ir_number;
        $reopen->status_id = $request->status_id;
        $reopen->request_status = $request->request_status;
        $reopen->requestor_id = $request->requestor_id;
        $reopen->approver_id = $request->approver_id;
        $reopen->reason = $request->reason;
        $reopen->save();

        broadcast(new OpenClosedCasesCreated($reopen));

        return $reopen;
    }

    public function reopenApproval(int $id, Request $request)
    {
        $respondent = Respondent::where('id', $id)
        ->update(['status_id' => $request->status_id]);

        $reopen = '';
        if ($respondent) {
            /*default  is_generate_nte_invalid_ir = 1 (Generate NTE)*/
            IncidentReport::where('id', $request->ir_id)
            ->update(['is_under_investigation' => 0, 'irr_id' => null, 'is_generate_nte_invalid_ir' => 1]);

            $reopen = ReopenRequest::where('respondent_id', $id)
            ->where('ir_id', $request->ir_id)
            ->update(['request_status' => 'Approved', 'approver_id' => $request->approver_id]);

            /* SoftDelete ProgressionOccurrence */
            ProgressionOccurrence::where('first_respondent_id', $id)
            ->orWhere('second_respondent_id', $id)
            ->orWhere('third_respondent_id', $id)
            ->orWhere('fourth_respondent_id', $id)
            ->orWhere('fifth_respondent_id', $id)
            ->orWhere('sixth_respondent_id', $id)
            ->orWhere('seventh_respondent_id', $id)
            ->delete();

            /* SoftDelete NteDaClosedStatus */
            NteDaClosedStatus::where('employee_id', $request->respondent_employee_no)
            ->where('description_of_the_offense', $request->offense)
            ->delete();
        }

        broadcast(new OpenClosedCasesCreated($reopen));

        return $reopen;
    }

    public function searchQuery($searchKey)
    {
       $designation = auth()->user()->designation->name;
       $user_emp_no = auth()->user()->employee_no;
       $user_id = auth()->user()->id;
       $roles = auth()->user()->roles[0]->name;

        $irhistory = Respondent::with([
            'complainant.complainant_user',
            'respondent_user',
            'respondent_user.designation',
            'respondent_user.department',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign',
            'complainant.witness_user.witness_fullname',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attendancepointssystem.attendancepoint',
            'complainant.attachments',
            'complainant.complainant_user',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.mom_attachment',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'respondent_hearing',
            'progression_occurence',
            'attached_hr',
            'reopen.requestor_name',
            'reopen.approver_name',
            'hrsl_user',
        ]);

        $irhistory = $irhistory->where('status_id', 4);
        $employee_no_array = [];
        $complainant_array_user_id = [];
        $UserSearch = User::where('first_name', 'LIKE' , '%'.$searchKey.'%')
        ->orWhere('last_name', 'LIKE' , '%'.$searchKey.'%')
        ->orWhere('employee_no', 'LIKE' , '%'.$searchKey.'%')
        ->get();

        foreach ($UserSearch as $key => $value) {
            array_push($employee_no_array, $value->id);
        }
        $complainant_user_id = Complainant::whereIn('complainant_user_id', $employee_no_array)->get();
        foreach ($complainant_user_id as $key => $value) {
            array_push($complainant_array_user_id, $value->id);
        }

        $irhistory = $irhistory->where('ir_number', 'LIKE', '%'.$searchKey.'%')
        ->orWhereIn('respondent_user_id', $employee_no_array)
        ->orWhereIn('complainant_id', $complainant_array_user_id);

        /* 1. Super Admin */
        if ($roles == "Super Admin Access") {
            $irhistory = $irhistory
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        } elseif ($roles == "HR Admin Access") { /* 2. HR Admin */
            $irhistory = $irhistory->where('hrsl_employee_no', $user_emp_no)
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        } elseif ($roles == "HR Access") { /* 3. HR Access */
            $irhistory = $irhistory->where('hrbp_employee_no', $user_emp_no)
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1
                || Str::contains($designation, 'Senior Operation Manager') == 1) {
                $irhistory = $irhistory
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            } elseif (Str::contains($designation, 'Resolution Manager') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['id'];
                }
                array_push($supArray, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            } else {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['id'];
                }
                array_push($supArray2, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray2)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            }
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Supervisor') == 1
        ||  Str::contains($designation, 'Team Lead') == 1) {
            /* 4. Regular Supervisor - Sup*/
            if (Str::contains($designation, 'Senior Operation Supervisor') == 1) {
                $irhistory = $irhistory
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            } else {
                $userSups = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($userSups as $userSup) {
                    $supArray[] = $userSup['employee_assign']['id'];
                }
                array_push($supArray, $user_id);

                $irhistory = $irhistory->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            }
        } else { /* 5. Regular User */
            $irhistory = $irhistory->where('respondent_user_id', auth()->id())
            ->orderBy('ir_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        }

        return response()->json($irhistory);
    }

    public function enableDa($ir_id)
    {
        $enableda = IncidentReport::where('id', $ir_id)
        ->update(['is_visible_respondent' => 1]);

        return $enableda;
    }
}
