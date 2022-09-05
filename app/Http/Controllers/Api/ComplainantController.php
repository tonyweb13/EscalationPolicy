<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;
use App\Models\{
    Complainant,
    Attachment,
    Respondent,
    Witness,
    User,
    HrbpCluster
};
use App\Models\Admin\Settings\Coc\{
    Category,
    Offense
};
use App\Jobs\EmailNewIncidentReport;
use App\Events\OpenClosedCasesCreated;
use Auth;

class ComplainantController extends Controller
{
    public function index() : JsonResponse
    {
        $complainant = Respondent::all();
        return response()->json($complainant);
    }

    public function create(Request $request)
    {
        $complainant = new Complainant;
        $complainant->date_incident_start = $request->date_incident_start;
        $complainant->date_incident_end = $request->date_incident_end;
        $complainant->description = $request->description;
        $complainant->remarks = $request->remarks;
        $complainant->attachment_type = (int)$request->attachment_type;

        if ((int)$request->complainant_user != Auth::user()->id) {
            $complainant->complainant_user_id = (int)$request->complainant_user;
            $complainant->creator_user_id = Auth()->id();
        } else {
            $complainant->complainant_user_id = Auth()->id();
            $complainant->creator_user_id = (int)$request->complainant_user;
        }

        if ($request->attendancepointssystem_id) {
            $complainant->attendancepointssystem_id = $request->attendancepointssystem_id;
        } elseif ($request->offense_multiple) {
            $complainant->offense_id = json_encode($request->offense_multiple);
        } else {
            $complainant->offense_id = $request->offense_id;
        }

        if ($complainant->save()) {
            if ($request->respondent_user_id) {
                foreach ($request->respondent_user_id as $respondent_user) {
                    $respondent = new Respondent;
                    $respondent->complainant_id = $complainant->id;
                    $respondent->respondent_user_id = $respondent_user;
                    // fixing new IR for ageing.
                    // $respondent->ageing = 1;
                    $respondent->status_id = 1; //Status: 1 = New
                    $respondent->new_ageing = 1; //New Ageing: 1 = New

                    if ($respondent->save()) {
                        $irInitial = STR_PAD($respondent->id, 9, '0', STR_PAD_LEFT);
                        $complainant->ir_number = $irInitial;

                        $getHrsl = $this->getRespondentHr($respondent->respondent_user_id, "HRSL");
                        $getHrbp = $this->getRespondentHr($respondent->respondent_user_id, "HRBP");

                        Respondent::where('id', $respondent->id)->update([
                            'ir_number' => $irInitial,
                            'hrsl_employee_no' => $getHrsl,
                            'hrbp_employee_no' => $getHrbp
                        ]);

                        /* not included on EP v2 - HR Meeting 05112019  */
                        // EmailNewIncidentReport::dispatch($complainant->ir_number, Auth()->user()->employee_no);
                    }
                }
            }

            if ($request->witness_user_id) {
                foreach ($request->witness_user_id as $witness_user) {
                    $witness = new Witness;
                    $witness->complainant_id = $complainant->id;
                    $witness->complainant_user_id = Auth()->id();
                    $witness->witness_user_id = $witness_user;
                    $witness->save();
                }
            }

            if (!is_null($request->attachments)) {
                $attach = new Attachment;
                $attach->complainant_id = $complainant->id;
                $attach->respondent_id = $respondent->id;
                $attach->attachments = json_encode($request->attachments);

                if ($attach->save()) {
                    Complainant::where('id', $complainant->id)
                    ->update([
                        'attachments_id' => $attach->id,
                    ]);
                }
            }
        }

        return $complainant;
    }

    public function attachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/complainants/'
                        . auth()->user()->employee_no
                        . '/' . Carbon::today()->format('Y-m-d')
                        . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'complainants/'
                            . auth()->user()->employee_no . '/'
                            . Carbon::today()->format('Y-m-d')
                            . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded ]);
        }
    }

    public function createDescription(Request $request)
    {
        if (strpos($request->description, "data:image/png;base64") !== false || strpos($request->description, "<img src") !== false) {
            $description = str_replace("<img src", "<img style='width:600px;' src", $request->description);
        } else {
            $description = $request->description;
        }

        $dateStart = preg_replace('/\(.*$/', '', $request->date_incident_start);
        $dateEnd = preg_replace('/\(.*$/', '', $request->date_incident_end);

        $complainant = new Complainant;
        $complainant->date_incident_start = Carbon::parse($dateStart)->format('Y-m-d');
        $complainant->date_incident_end = Carbon::parse($dateEnd)->format('Y-m-d');
        $complainant->remarks = $request->remarks;
        $complainant->attachment_type = (int)$request->attachment_type;
        $complainant->description = $description;

        if ((int)$request->complainant_user != Auth()->id()) {
            $complainant->complainant_user_id = (int)$request->complainant_user;
            $complainant->creator_user_id = Auth()->id();
        } else {
            $complainant->complainant_user_id = Auth()->id();
            $complainant->creator_user_id = (int)$request->complainant_user;
        }

        if ($request->attendancepointssystem_id) {
            $complainant->attendancepointssystem_id = $request->attendancepointssystem_id;
        } elseif ($request->offense_multiple) {
            $offense_array = explode(",", $request->offense_multiple);
            $complainant->offense_id = json_encode($offense_array);
        } else {
            $complainant->offense_id = $request->offense_id;
        }

        $complainant->save();

        if ($complainant->id) {
            if ($request->respondent_user_id) {

                $respondent_array = explode(",", $request->respondent_user_id);
                foreach ($respondent_array as $respondent_user) {
                    $respondent = new Respondent;
                    $respondent->complainant_id = $complainant->id;
                    $respondent->respondent_user_id = $respondent_user;
                    $respondent->status_id = 1; //Status: 1 = New
                    $respondent->new_ageing = 1; //New Ageing: 1 = New
                    $respondent->save();

                    if ($respondent->id) {
                        $irInitial = STR_PAD($respondent->id, 9, '0', STR_PAD_LEFT);
                        $complainant->ir_number = $irInitial;

                        $getHrsl = $this->getRespondentHr($respondent->respondent_user_id, "HRSL");
                        $getHrbp = $this->getRespondentHr($respondent->respondent_user_id, "HRBP");

                        Respondent::where('id', $respondent->id)->update([
                            'ir_number' => $irInitial,
                            'hrsl_employee_no' => $getHrsl,
                            'hrbp_employee_no' => $getHrbp
                        ]);
                    }
                }
            }

            if ($request->witness_user_id) {
                $witness_array = explode(",", $request->witness_user_id);

                foreach ($witness_array as $witness_user) {
                    $witness = new Witness;
                    $witness->complainant_id = $complainant->id;
                    $witness->complainant_user_id = Auth()->id();
                    $witness->witness_user_id = $witness_user;
                    $witness->save();
                }
            }

            if (!is_null($request->attachments)) {
                $attach = new Attachment;
                $attach->complainant_id = $complainant->id;
                $attach->respondent_id = $respondent->id;
                $attach->attachments = json_encode($request->attachments);

                if ($attach->save()) {
                    Complainant::where('id', $complainant->id)
                    ->update([
                        'attachments_id' => $attach->id,
                    ]);
                }
            }
        }

        broadcast(new OpenClosedCasesCreated(1));

        return $complainant;
    }

    public function getRespondentHr($id, $hr)
    {
        $getEmployeeNo = User::findOrFail($id);
        $getHr = HrbpCluster::where('user_employee_no', $getEmployeeNo['employee_no'])->first();

        if ($hr == 'HRSL') {
            if($getHr['hrsl_employee_no']){
                return $getHr['hrsl_employee_no'];
            } else {
                /* When cannot find HR Site Lead, assign to dedicated HR (Hazel) */
                $getDedicated = HrbpCluster::where('hrbp_email_address', 'LIKE', '%hazel%')->first();
                return $getDedicated['hrbp_employee_no'];
            }
        }

        if ($hr == 'HRBP') {
            if($getHr['hrsl_employee_no']){
                return $getHr['hrbp_employee_no'];
            } else {
                /* When cannot find HRBP, assign to dedicated HR (Hazel) */
                $getDedicated = HrbpCluster::where('hrbp_email_address', 'LIKE', '%hazel%')->first();
                return $getDedicated['hrbp_employee_no'];
            }
        }
    }

    public function complainantEdit(Request $request)
    {
        $complainant = Complainant::where('id', $request->complainant_id)
        ->with([
            'offense.gravity', 'respondent',
            'offense.category', 'witness_user',
            'attachments', 'attendancepointssystem'
        ])->first();

        if ($request->multiple_offense_new) {
            $new_multis = explode(",",$request->multiple_offense_new);
            $new_multi2 = json_encode($new_multis);
            $change_data = [];
            if ($new_multi2 != $complainant->offense_id) {
                $change_data['offense_id'] = $new_multi2;
            }
        } else {
            if ($request->offense_id > 0 && $request->offense_id != $complainant->offense->id) {
                $change_data['offense_id'] = $request->offense_id;
            }
        }

        if ($request->date_incident_start != $complainant->date_incident_start) {
            $change_data['date_incident_start'] = $request->date_incident_start;
        }
        if ($request->date_incident_end != $complainant->date_incident_end) {
            $change_data['date_incident_end'] = $request->date_incident_end;
        }
        if ($request->description != $complainant->description) {
            $change_data['description'] = $request->description;
        }
        if (!empty($change_data)) {
            $complainant = Complainant::where('id', $request->complainant_id)->update($change_data);
        }
    }
}
