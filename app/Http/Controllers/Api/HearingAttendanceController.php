<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{HearingAttendance, Invite};
use App\Models\Admin\IncidentReport;
use Carbon\Carbon;

class HearingAttendanceController extends Controller
{
    public function create(Request $request)
    {
        $hearing = new HearingAttendance;
        $hearing->going = $request->going;
        $hearing->ir_id = $request->ir_id;

        /* going = no */
        $hearing->requestor = $request->requestor;
        $hearing->requestor_user_id = $request->requestor_user_id;
        $hearing->reason = $request->reason;
        $hearing->suggested_date = $request->suggested_date;
        $hearing->suggested_time = $request->suggested_time;
        $hearing->suggested_place = $request->suggested_place;
        $hearing->hr_approval = $request->hr_approval;
        if ($request->going == 'Yes') {
            $hearing->accepted_date = Carbon::now();

        }
        $hearing->save();

        return $hearing;
    }

    public function hrApproval(int $id, Request $request)
    {
        if ($request->hr_approval == "Approve") {
            $hearing = HearingAttendance::where('id', $id)
                ->where('ir_id', $request->ir_id)
                ->where('requestor_user_id', $request->requestor_user_id)
                ->update([
                    'hr_approval' => $request->hr_approval,
                    'going' => $request->going
                ]);

                IncidentReport::where('id', $request->ir_id)
                ->update([
                    'date_admin_hearing' => $request->suggested_date,
                    'time_admin_hearing' => $request->suggested_time,
                    'meeting_place' => $request->suggested_place
                ]);

        } else {
            /* hr disapprove */
            $hearing = HearingAttendance::where('id', $id)
                ->where('ir_id', $request->ir_id)
                ->where('requestor_user_id', $request->requestor_user_id)
                ->update([
                    'hr_approval' => $request->hr_approval,
                    'going' => $request->going
                ]);
        }

        return $hearing;
    }

    public static function destroy(int $id)
    {
        $hearing = HearingAttendance::where('ir_id', $id)->delete();

        return response()->json($hearing);
    }
}
