<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Admin\Settings\Coc\IncidentReportResolution,
    Admin\Settings\Coc\Category,
    Admin\Settings\Coc\Offense,
    Respondent,
    NteDaClosedStatus,
    UsersGroup,
    User

};
use App\Models\Admin\Settings\Coc\{YearlyTotalInfraction, MonthlyTotalInfraction};
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Admin\Settings\Coc\OffenseController;
use App\Http\Controllers\Api\Admin\IncidentReportController;

class ClosedStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $user_emp_no = auth()->user()->employee_no;
        $designation = auth()->user()->designation->name;
        $roles = auth()->user()->roles[0]->name;
        $user_fullname = auth()->user()->first_name. ' ' . auth()->user()->last_name;

        if ($roles == "Super Admin Access") { /* 1. Super Admin Access*/
            // $closed = NteDaClosedStatus::orderBy('created_at', 'desc')->get();
            $closed = NteDaClosedStatus::orderBy('created_at', 'desc')->paginate(10);
        } elseif ($roles == 'HR Admin Access') { /* 2. HR Admin */
            $closed = NteDaClosedStatus::whereHas('closed_hr', function ($query) use ($user_emp_no) {
                $query->where('hrsl_employee_no', $user_emp_no);
            })->paginate(10);
        } elseif ($roles == 'HR Access') { /* 3. HR Access */
            $closed = NteDaClosedStatus::whereHas('closed_hr', function ($query) use ($user_emp_no) {
                $query->where('hrbp_employee_no', $user_emp_no);
            })->paginate(10);
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1) {
                $closed = NteDaClosedStatus::where('rank', 'LIKE', '%Agent%')
                ->orWhere('rank', 'LIKE', '%Originator%')
                ->orWhere('rank', 'LIKE', '%Supervisor%')
                ->paginate(10);
            } else {
                $closed = NteDaClosedStatus::where('dm', 'LIKE', '%'.$user_fullname.'%')->paginate(10);
            }
        } elseif (Str::contains($designation, 'Supervisor') == true ||
        Str::contains($designation, 'Team Lead') == true) { /* 4. Regular Supervisor - Sup*/
            $closed = NteDaClosedStatus::where('immediate_supervisor', 'LIKE',
            '%'.$user_fullname.'%')->paginate(10);
        } else { /* 5. Regular User */
            $closed = NteDaClosedStatus::where('employee_id', $user_emp_no)->paginate(10);
        }
        $offense = IncidentReportController::getOffenseOnlyTrashed();
        foreach ($closed as $value) {
            if ($value->da_imposed) {
                $irr_exist = $this->IncidentReportResolutionCheckIfExist($value->da_imposed);
                $value['irr_exist'] = $irr_exist;
            }
            if ($value->description_of_the_offense) {
                $offense_exist = $this->offenseCheckIfExist($value->description_of_the_offense);
                $value['offense_exist'] = $offense_exist;
            }
        }

        return response()->json($closed);
    }

    private function IncidentReportResolutionCheckIfExist(String $offense)
    {
        $exist = IncidentReportResolution::withTrashed()->where('name', $offense)->first();
        if ($exist->deleted_at) {
            $irr_exist = 'Corrective Action Already archived';
        }

        return $irr_exist;
    }

    private function offenseCheckIfExist(String $offense)
    {
        $exist = Offense::withTrashed()->where('offense', $offense)->first();
        if ($exist->deleted_at) {
            $offense_exist = 'Already archived';
        }

        return $offense_exist;
    }

    // public function create(Request $request)
    // {
    //     $respondent = Respondent::with([
    //         'respondent_user.designation',
    //         'respondent_user.office_location',
    //         'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign',
    //         'complainant.offense',
    //         'complainant.attendancepointssystem.category',
    //         'complainant.offense.category',
    //         'complainant.offense.gravity',
    //         'incident_report.hrbp_user',
    //         'incident_report.irr',
    //     ])
    //     ->findOrFail($request->respondent_id);

    //     echo "irr->name==".$respondent->incident_report->irr->name;
    //     echo "ageing==".$respondent->ageing;
    //     dd($respondent);
    //     exit();

    //     $data['offense_ids'] = json_decode(str_replace('"', '', $respondent->complainant->offense_id));

    //     if (count($data['offense_ids']) > 1) {
    //         /* start  offense_multiple */
    //         $multi = [];
    //         for ($c=0; $c < count($data['offense_ids']); $c++) {
    //             $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
    //             array_push($multi, $multiple);
    //         }

    //         foreach ($multi as $m) {
    //             $this->saveNteDaClosed($respondent, $request, $m->category->name, $m->offense,
    //             $m->gravity->gravity);
    //         }
    //         /* end  offense_multiple */
    //     } else {
    //         $this->saveNteDaClosed($respondent, $request, '', '', '');
    //     }
    // }

    public static function createNteDAClosed(Request $request)
    {
        $respondent = Respondent::with([
            'respondent_user.designation',
            'respondent_user.office_location',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign',
            'complainant.offense',
            'complainant.attendancepointssystem.category',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'incident_report.hrbp_user'
        ])
        ->findOrFail($request->respondent_id);

        $data['offense_ids'] = json_decode(str_replace('"', '', $respondent->complainant->offense_id));

        if (count($data['offense_ids']) > 1) {
            /* start  offense_multiple */
            $multi = [];
            for ($c=0; $c < count($data['offense_ids']); $c++) {
                $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                array_push($multi, $multiple);
            }

            foreach ($multi as $key => $m) {
                ClosedStatusController::saveNteDaClosed($respondent, $request, $m->category->name,
                $m->offense, $m->gravity->gravity, $request->irr_multiple[$key]);
            }
            /* end  offense_multiple */
        } else {
            ClosedStatusController::saveNteDaClosed($respondent, $request, '', '', '', '');
        }
    }

    public static function createYearlyMonthlyRecord($ir_record, $table)
    {
        $infration = $table;
        $infration->employee_no = $ir_record['employee_no'];
        $infration->last_name = $ir_record['last_name'];
        $infration->first_name = $ir_record['first_name'];
        $infration->grave = self::checkGravityFalls($ir_record['gravity'], 'Grave');
        $infration->major = self::checkGravityFalls($ir_record['gravity'], 'Major');
        $infration->minor = self::checkGravityFalls($ir_record['gravity'], 'Minor');
        $infration->serious = self::checkGravityFalls($ir_record['gravity'], 'Serious');
        $infration->grand_total = 1;
        $infration->save();
    }

    public static function checkGravityFalls($gravity, $condition_checker, $gravity_count = 0)
    {
        $data = null;
        $gravity_count == null ? $gravity_count : 0;
        if ($gravity == $condition_checker) {
            $data = $gravity_count + 1;
        }

        return $data;
    }

    public static function updateYearlyMonthlyRecord($table, $ir_gravity, $ir_record)
    {
        $table->update([
            'grave' =>  self::checkGravityFalls($ir_gravity, 'Grave', $ir_record->grave),
            'minor' => self::checkGravityFalls($ir_gravity, 'Minor', $ir_record->minor),
            'major' => self::checkGravityFalls($ir_gravity, 'Major', $ir_record->major),
            'serious' => self::checkGravityFalls($ir_gravity, 'Serious', $ir_record->serious),
            'grand_total' => $ir_record->grand_total + 1
        ]);
    }

    public static function createOrUpdateInfractionMonthOrYearly($data, $checkMonthOrYear)
    {
        $infraction = [];
        if ($checkMonthOrYear == 'month') {
            $infraction = MonthlyTotalInfraction::where('employee_no', $data['employee_no'])
                ->whereMonth('created_at', date('m'))
                ->first();
            $table = new MonthlyTotalInfraction;
        }
        if ($checkMonthOrYear == 'year') {
            $infraction = YearlyTotalInfraction::where('employee_no', $data['employee_no'])
                ->whereYear('created_at', date('Y'))
                ->first();
            $table = new YearlyTotalInfraction;
        }

        if (empty($infraction)) {
            self::createYearlyMonthlyRecord($data, $table);
        } else {
            $total_infraction = [];
            if ($checkMonthOrYear == 'month') {
                $total_infraction = MonthlyTotalInfraction::where('employee_no', $data['employee_no']);
            }
            if ($checkMonthOrYear == 'year') {
                $total_infraction = YearlyTotalInfraction::where('employee_no', $data['employee_no']);
            }
            self::updateYearlyMonthlyRecord($total_infraction, $data['gravity'], $infraction);
        }
    }

    public function exportClosedDa()
    {
        $user_emp_no = auth()->user()->employee_no;
        $roles = auth()->user()->roles[0]->name;

        ini_set('memory_limit', '4095M');
        if ($roles == 'HR Admin Access') {
            $closed = NteDaClosedStatus::whereHas('closed_hr', function ($query) use ($user_emp_no) {
                $query->where('hrsl_employee_no', $user_emp_no);
            })->get();
        } else {
            /* Super Admin Access */
            $closed = NteDaClosedStatus::all();
        }

        $data_return = [];
        foreach ($closed as $value) {
            $value['description_of_the_offense'] = strip_tags(str_replace("</p>", "\r\n",
            $value->description_of_the_offense));
            $value['notes_for_case'] = strip_tags(str_replace("</p>", "\r\n", $value->notes_for_case));
            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    private function cleanDataForFilter($closed)
    {
        $closed_json = [];
        foreach ($closed as $value) {
            if (Str::contains(strtolower($value->site), 'market') !== false) {
                $value->site = 'Market Market';
            }
            if (Str::contains(strtolower($value->position), 'operation') !== false) {
                $value->position = 'Operation Supervisor';
            }
            if (Str::contains(strtolower($value->site), 'net') !== false) {
                $value->site = 'Net Square';
            }
            array_push($closed_json, $value);
        }

        return $closed_json;
    }

    public static function saveNteDaClosed($respondent, $request, $multi_category, $multi_offense,
    $multi_gravity, $multi_corrective_action)
    {
        $closed = new NteDaClosedStatus;
        $closed->ir_number = $respondent->ir_number;
        $closed->employee_id = $respondent->respondent_user->employee_no;
        $closed->last_name = $respondent->respondent_user->last_name;
        $closed->first_name = $respondent->respondent_user->first_name;
        $closed->position = $respondent->respondent_user->designation->name;

        if (preg_match("/(market)/i", $respondent->respondent_user->office_location->name)){
            $closed->site = "Market Market";

        } else if (preg_match("/(net|bgc|two|neo)/i", $respondent->respondent_user->office_location->name)){
            $closed->site = "Two Neo";

        } else if (preg_match("/(bacolod|bcd)/i", $respondent->respondent_user->office_location->name)){
            $closed->site = "Bacolod";

        } else if (preg_match("/(alabang)/i", $respondent->respondent_user->office_location->name)){
            $closed->site = "Alabang";

        } else {
            $closed->site = $respondent->respondent_user->office_location->name;
        }

        $closed->hrbp = $respondent->incident_report->hrbp_user->first_name
        . " " .
        $respondent->incident_report->hrbp_user->last_name;

        $closed->immediate_supervisor =
        $respondent->respondent_user->employee_supervisor->supervisor_assign->first_name . " " .
        $respondent->respondent_user->employee_supervisor->supervisor_assign->last_name;

        $closed->dm = $respondent->respondent_user->employee_supervisor->supervisor_assign->employee_supervisor
        ->supervisor_assign->first_name  . " " . $respondent->respondent_user->employee_supervisor
        ->supervisor_assign->employee_supervisor->supervisor_assign->last_name;

        if ($respondent->complainant->date_incident_end) {
            $closed->date_of_offense =
            Carbon::parse($respondent->complainant->date_incident_start)->format("d-M-y") . " to " .
            Carbon::parse($respondent->complainant->date_incident_end)->format("d-M-y");
        } else {
            $closed->date_of_offense = Carbon::parse($respondent->complainant->date_incident_start)
            ->format("d-M-y");
        }

        $closed->nte_request = Carbon::parse($respondent->complainant->created_at)->format("d-M-y");
        $closed->date_nte_draft = Carbon::parse($respondent->incident_report->created_at)->format("d-M-y");

        if ($multi_category && $multi_offense && $multi_gravity) {
            $closed->coc_provision = $multi_category;
            $closed->description_of_the_offense = $multi_offense;
            $closed->gravity = $multi_gravity;
        } elseif ($respondent->complainant->offense) {
            $closed->coc_provision = $respondent->complainant->offense->category->name;
            $closed->description_of_the_offense = $respondent->complainant->offense->offense;
            $closed->gravity = $respondent->complainant->offense->gravity->gravity;
        } elseif ($respondent->complainant->attendancepointssystem) {
            $closed->coc_provision = $respondent->complainant->attendancepointssystem->category->name;
            $closed->gravity = $respondent->complainant->attendancepointssystem->attendancepoint->attendance_points;
            $closed->description_of_the_offense = $respondent->complainant->attendancepointssystem->type_infraction;
        }

        $closed->nte_date_served = Carbon::parse($respondent->incident_report->updated_at)->format("d-M-y");
        $closed->date_hr_received = Carbon::parse($respondent->updated_at)->format("d-M-y");
        $closed->date_admin_hearing =
        Carbon::parse($respondent->incident_report->date_admin_hearing)->format("d-M-y");

        /* multiple irr*/
        if (count($request->irr_multiple) > 1) {
            $irr = IncidentReportResolution::where("id", $multi_corrective_action)->first();
            $closed->da_imposed = $irr->name;
        } else {
            $irr = IncidentReportResolution::where("id", $request->irr_id)->first();
            $closed->da_imposed = $irr->name;
        }
        $closed->date_da = Carbon::now()->format("d-M-y");

        if ($request->stage_case == "Closed for 201 Filing" 
        || $request->stage_case == "Closed for 201 Filling"
        || $request->stage_case == '{"value":3,"text":"Closed for 201 Filing"}' 
        || $request->stage_case == '{"value":3,"text":"Closed for 201 Filling"}') {
            $stage_case = "Closed for 201 Filing";

        } else if ($request->stage_case == "Closed - Resigned" 
        || $request->stage_case == '{"value":1,"text":"Closed - Resigned"}') {
            $stage_case = "Closed - Resigned";

        } else if ($request->stage_case == "Closed - Terminated" 
        || $request->stage_case == '{"value":2,"text":"Closed - Terminated"}') {
            $stage_case = "Closed - Terminated";

        } else {
            $stage_case = $request->stage_case;
        }
        $closed->stage_of_the_case = $stage_case;
        $closed->notes_for_case = $respondent->incident_report->remarks;
        $closed->case_status = "Closed";
        $closed->ageing = $respondent->ageing;

        $tat = 0;
        if ($respondent->ageing > 16 &&
        ($respondent->complainant->offense->gravity->gravity == "Grave" ||
        $respondent->complainant->offense->gravity->gravity == "Serious")) {
            $exceeded = 16;
            $tat = $respondent->ageing - $exceeded;

            if ($tat > 0) {
                $tat_status = 'Exceeded TAT';
            }

        } elseif ($respondent->ageing > 12 &&
        ($respondent->complainant->offense->gravity->gravity == "Major" ||
        $respondent->complainant->offense->gravity->gravity == "Minor")) {
            $exceeded = 12;
            $tat = $respondent->ageing - $exceeded;

            if ($tat > 0) {
                $tat_status = 'Exceeded TAT';
            }
        } else {
            $tat_status = 'Within TAT';
        }

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('F');
        $week = "WE " . Carbon::now()->startOfWeek()->format('M-d')
        . " - " . Carbon::now()->endOfWeek()->format('M-d');

        /* Start Quarter */
        $quarter = '';
        if ($month == 'January' || $month == 'February' || $month == 'March') {
            $quarter = 'Q1';
        } elseif ($month == 'April' || $month == 'May' || $month == 'June') {
            $quarter = 'Q2';
        } elseif ($month == 'July' || $month == 'August' || $month == 'September') {
            $quarter = 'Q3';
        } elseif ($month == 'October' || $month == 'November' || $month == 'December') {
            $quarter = 'Q4';
        }

        /* Start RANK */
        if (Str::contains($respondent->respondent_user->designation->name, 'Specialist') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Analyst') == true
            || Str::contains($respondent->respondent_user->designation->name, 'SME') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Mentor') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Trainer') == true
            || Str::contains($respondent->respondent_user->designation->name, 'HR') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Manager') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Collection') == true
        ) {
            $rank = 'Ops Support';
        } elseif (Str::contains($respondent->respondent_user->designation->name, 'Designer') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Support') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Programmer') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Developer') == true
            || Str::contains($respondent->respondent_user->designation->name, 'IT') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Accountant') == true
            || Str::contains($respondent->respondent_user->designation->name, 'Compensation and Benefit') == true
        ) {
            $rank = 'Shared Services';
        } elseif (Str::contains($respondent->respondent_user->designation->name, 'Supervisor') == true
        || Str::contains($respondent->respondent_user->designation->name, 'Team Lead') == true) {
            $rank = 'Supervisor';
        } else {
            $rank = 'Agent';
        }

        $closed->days_exceeded_tat = $tat;
        $closed->tat_status = $tat_status;
        $closed->year_nte_draft = $year;
        $closed->month_nte_draft = Carbon::parse($respondent->incident_report->created_at)->format("M");
        $closed->week = $week;
        $closed->quarter = $quarter;
        $closed->rank = $rank;
        $closed->save();
    }

    public function updateClosedIrnumber()
    {
        $closed = NteDaClosedStatus::whereNull('ir_number')->get();
        $respondent = Respondent::with(['respondent_user', 'complainant.offense', 'complainant.offense.category',
        'complainant.offense.gravity', 'complainant.complainant_user', 'incident_report.irr'])->get();

        foreach ($closed as $close) {
            foreach ($respondent as $res) {
                if ($res->respondent_user->first_name == $close->first_name
                && $res->respondent_user->last_name == $close->last_name
                && $res->respondent_user->employee_no == $close->employee_id
                && $res->complainant->offense->offense == $close->description_of_the_offense
                && $res->complainant->offense->category->name == $close->coc_provision
                && $res->complainant->offense->gravity->gravity == $close->gravity
                && $res->incident_report->irr->name == $close->da_imposed
                && $res->hrbp_user->first_name." ".$res->hrbp_user->last_name == $close->hrbp) {
                    NteDaClosedStatus::where('first_name', $close->first_name)
                    ->where('last_name', $close->last_name)
                    ->where('employee_id', $close->employee_id)
                    ->where('description_of_the_offense', $close->description_of_the_offense)
                    ->where('coc_provision', $close->coc_provision)
                    ->where('gravity', $close->gravity)
                    ->where('da_imposed', $close->da_imposed)
                    ->where('hrbp', $close->hrbp)
                    ->update(['ir_number' => $res->ir_number]);
                }
            }
        }
    }

    public function exportClosedFilter($cat, $subcat)
    {
        $closed = NteDaClosedStatus::where($cat, 'LIKE', '%'.$subcat.'%')->get();

        $data_return = [];
        foreach ($closed as $value) {
            $value['description_of_the_offense'] = strip_tags(str_replace("</p>", "\r\n",
            $value->description_of_the_offense));
            $value['notes_for_case'] = strip_tags(str_replace("</p>", "\r\n", $value->notes_for_case));
            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    public function exportClosedFilterThird($cat, $subcat, $thirdcat)
    {
        $closed = NteDaClosedStatus::where($cat, 'LIKE', '%'.$subcat.'%')
        ->where('year_nte_draft', $thirdcat)
        ->get();

        $data_return = [];
        foreach ($closed as $value) {
            $value['description_of_the_offense'] = strip_tags(str_replace("</p>", "\r\n",
            $value->description_of_the_offense));
            $value['notes_for_case'] = strip_tags(str_replace("</p>", "\r\n", $value->notes_for_case));
            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    public function exportClosedDateRange(Request $request)
    {
        $s = Carbon::parse($request->start_range)->addDays(1)->format("Y-m-d");
        $e = Carbon::parse($request->end_range)->addDays(1)->format("Y-m-d");

        $closed = NteDaClosedStatus::whereRaw("DATE_FORMAT(STR_TO_DATE(`date_nte_draft`,'%d-%b-%y'), '%Y-%m-%d')
        between '".$s."' and '".$e."'")
        ->orderBy('date_nte_draft')
        ->get();

        $data_return = [];
        foreach ($closed as $value) {
            $value['description_of_the_offense'] = strip_tags(str_replace("</p>", "\r\n",
            $value->description_of_the_offense));
            $value['notes_for_case'] = strip_tags(str_replace("</p>", "\r\n", $value->notes_for_case));
            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    public function dropdownSupervisor()
    {
        $sup = User::select(DB::raw('CONCAT(first_name, " ", last_name) AS value'),
        DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Supervisor%');
            $query->orWhere('name', 'LIKE', '%Team Lead%');
        })->get();

        return response()->json($sup);
    }

    public function dropdownManager()
    {
        $man = User::select(DB::raw('CONCAT(first_name, " ", last_name) AS value'),
        DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Manager%');
        })->get();

        return response()->json($man);
    }

    public function dropdownProvision()
    {
        $cat = Category::select('name AS value', 'name AS text')->get();

        return response()->json($cat);
    }

    public function dropdownOffense()
    {
        $offense = Offense::select('offense AS value', 'offense AS text')->get();

        return response()->json($offense);
    }

    public function searchQuery($searchKey)
    {
        $user_emp_no = auth()->user()->employee_no;
        $designation = auth()->user()->designation->name;
        $roles = auth()->user()->roles[0]->name;
        $user_fullname = auth()->user()->first_name. ' ' . auth()->user()->last_name;

        $closed = NteDaClosedStatus::where('employee_id', 'LIKE' , '%'.$searchKey.'%')
        ->orWhere('first_name', 'LIKE' , '%'.$searchKey.'%')
        ->orWhere('last_name', 'LIKE' , '%'.$searchKey.'%');

        if ($roles == "Super Admin Access") { /* 1. Super Admin Access*/
            $closed = $closed->orderBy('created_at', 'desc')->get();
        } elseif ($roles == 'HR Admin Access') { /* 2. HR Admin */
            $closed = $closed->whereHas('closed_hr', function ($query) use ($user_emp_no) {
                $query->where('hrsl_employee_no', $user_emp_no);
            })->get();
        } elseif ($roles == 'HR Access') { /* 3. HR Access */
            $closed = $closed->whereHas('closed_hr', function ($query) use ($user_emp_no) {
                $query->where('hrbp_employee_no', $user_emp_no);
            })->get();
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1) {
                $closed = $closed->where('rank', 'LIKE', '%Agent%')
                ->orWhere('rank', 'LIKE', '%Originator%')
                ->orWhere('rank', 'LIKE', '%Supervisor%')
                ->get();
            } else {
                $closed = $closed->where('dm', 'LIKE', '%'.$user_fullname.'%')->get();
            }
        } elseif (Str::contains($designation, 'Supervisor') == true ||
        Str::contains($designation, 'Team Lead') == true) { /* 4. Regular Supervisor - Sup*/
            $closed = $closed->where('immediate_supervisor', 'LIKE', '%'.$user_fullname.'%')->get();
        } else { /* 5. Regular User */
            $closed = $closed->where('employee_id', $user_emp_no)->get();
        }

        return response()->json($closed);
    }

    public function exportOpenCases()
    {
        $user_emp_no = auth()->user()->employee_no;
        $roles = auth()->user()->roles[0]->name;

        ini_set('memory_limit', '4095M');
        if ($roles == 'HR Admin Access') {
            $open = Respondent::with([
                'complainant.complainant_user',
                'complainant.witness_user.witness_fullname',
                'complainant.offense',
                'complainant.offense.category',
                'complainant.offense.gravity',
                'complainant.attachments',
                'respondent_user',
                'hrbp_user',
                'respondent_user.designation',
                'respondent_user.office_location',
                'respondent_user.employee_supervisor.supervisor_assign.designation',
                'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
                'incident_report.irr',
                'incident_report.invite_user.invite_fullname',
                'incident_report.witness_user.witness_fullname',
                'incident_report.invite_hearing.invite_ir_fullname',
                'respondent_hearing'
            ])->where('status_id', '!=', 4)
            ->where('status_id', '!=', 0)
            ->where('hrsl_employee_no', $user_emp_no)
            ->orderBy('status_id', 'ASC')
            ->orderBy('ir_number', 'DESC')
            ->get();

        } else {
            /* Super Admin Access */
            $open = Respondent::with([
                'complainant.complainant_user',
                'complainant.witness_user.witness_fullname',
                'complainant.offense',
                'complainant.offense.category',
                'complainant.offense.gravity',
                'complainant.attachments',
                'respondent_user',
                'hrbp_user',
                'respondent_user.designation',
                'respondent_user.office_location',
                'respondent_user.employee_supervisor.supervisor_assign.designation',
                'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
                'incident_report.irr',
                'incident_report.invite_user.invite_fullname',
                'incident_report.witness_user.witness_fullname',
                'incident_report.invite_hearing.invite_ir_fullname',
                'respondent_hearing'
            ])->where('status_id', '!=', 4)
            ->where('status_id', '!=', 0)
            ->orderBy('status_id', 'ASC')
            ->orderBy('ir_number', 'DESC')
            ->get();
        }

        $data_return = [];
        foreach ($open as $value) {

            $tat = 0;
            if ($value->ageing > 16 &&
            ($value->complainant->offense->gravity->gravity == "Grave" ||
            $value->complainant->offense->gravity->gravity == "Serious")) {
                $exceeded = 16;
                $tat = $value->ageing - $exceeded;

                if ($tat > 0) {
                    $value['TAT'] = 'Exceeded TAT';
                }

            } elseif ($value->ageing > 12 &&
            ($value->complainant->offense->gravity->gravity == "Major" ||
            $value->complainant->offense->gravity->gravity == "Minor")) {
                $exceeded = 12;
                $tat = $value->ageing - $exceeded;

                if ($tat > 0) {
                    $value['TAT'] = 'Exceeded TAT';
                }
            } else {
                $value['TAT'] = 'Within TAT';
            }

            $value['remarks'] = strip_tags(str_replace("</p>", "\r\n", $value->incident_report->remarks));
            $value['response'] = strip_tags(str_replace("</p>", "\r\n", $value->response));

            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    public function exportIrHistory()
    {
        ini_set('memory_limit', '4095M');
        ini_set('max_execution_time', '600');
        $irhist = Respondent::with([
            'complainant.complainant_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attachments',
            'respondent_user',
            'hrbp_user',
            'respondent_user.designation',
            'respondent_user.office_location',
            'respondent_user.employee_supervisor.supervisor_assign.designation',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
            'incident_report.irr'
        ])->where('status_id', 4)
        ->where('created_at', '>=', Carbon::now()->submonths(3))
        ->orderBy('ir_number', 'DESC')
        ->get();

        $data_return = [];
        foreach ($irhist as $value) {
            $value['employee_id'] = $value->respondent_user->employee_no;
            $value['last_name'] = $value->respondent_user->last_name;
            $value['first_name'] = $value->respondent_user->first_name;
            $value['position'] = $value->respondent_user->designation->name;
            $value['site'] = $value->respondent_user->office_location->name;
            $value['hrbp'] = $value->hrbp_user->first_name . " " . $value->hrbp_user->last_name;
            $value['immediate_supervisor'] =
            $value->respondent_user->employee_supervisor->supervisor_assign->first_name . " " .
            $value->respondent_user->employee_supervisor->supervisor_assign->last_name;
            $value['dm'] = $value->respondent_user->employee_supervisor->supervisor_assign->employee_supervisor
            ->supervisor_assign->first_name  . " " . $value->respondent_user->employee_supervisor
            ->supervisor_assign->employee_supervisor->supervisor_assign->last_name;
            $value['nte_request'] = Carbon::parse($value->complainant->created_at)->format("d-M-y");
            $value['date_nte_draft'] = Carbon::parse($value->incident_report->created_at)->format("d-M-y");
            $value['da_imposed'] = $value->incident_report->irr->name;
            $value['nte_date_served'] = Carbon::parse($value->incident_report->updated_at)->format("d-M-y");
            $value['date_hr_received'] = Carbon::parse($value->updated_at)->format("d-M-y");
            $value['date_admin_hearing'] = $value->incident_report->date_admin_hearing;
            $value['date_da'] = $value->incident_report->date_da;
            $value['notes_for_case'] = strip_tags($value->incident_report->remarks);
            $value['case_status'] = "Closed";
            $value['ageing'] = $value->ageing;

            $offense_ids = json_decode(str_replace('"', '', $value->complainant->offense_id));
            if (count($offense_ids) > 1) {
                /* start multi offense */
                $multi = [];
                for ($c=0; $c < count($offense_ids); $c++) {
                    $multiple = OffenseController::codeofconduct($offense_ids[$c]);
                    array_push($multi, $multiple);
                }
                foreach ($multi as $key => $m) {
                    $value['coc_provision'] = $m->category->name;
                    $value['description_of_the_offense'] = $m->offense;
                    $value['gravity'] = $m->gravity->gravity;
                }
                 /* end multi offense */
            } elseif ($value->complainant->offense) {
                /* start single offense */
                $value['coc_provision'] = $value->complainant->offense->category->name;
                $value['description_of_the_offense'] = $value->complainant->offense->offense;
                $value['gravity'] = $value->complainant->offense->gravity->gravity;
                /* end single offense */
            }

            $nteDaClosed = NteDaClosedStatus::where("ir_number", $value->ir_number)
            ->where("employee_id",  $value->respondent_user->employee_no)
            ->where("description_of_the_offense",  $value['description_of_the_offense'])
            ->first();

            $value['stage_of_the_case'] = $nteDaClosed->stage_of_the_case;

            if ($value->complainant->date_incident_end) {
                $date_of_offense =
                Carbon::parse($value->complainant->date_incident_start)->format("d-M-y") . " to " .
                Carbon::parse($value->complainant->date_incident_end)->format("d-M-y");
            } else {
                $date_of_offense = Carbon::parse($value->complainant->date_incident_start)
                ->format("d-M-y");
            }

            $value['date_of_offense'] = $nteDaClosed->date_of_offense ? 
            $nteDaClosed->date_of_offense : $date_of_offense;

            $tat = 0;
            if ($value->ageing > 16 &&
            ($value->complainant->offense->gravity->gravity == "Grave" ||
            $value->complainant->offense->gravity->gravity == "Serious")) {
                $exceeded = 16;
                $tat = $value->ageing - $exceeded;
    
                if ($tat > 0) {
                    $tat_status = 'Exceeded TAT';
                }
    
            } elseif ($value->ageing > 12 &&
            ($value->complainant->offense->gravity->gravity == "Major" ||
            $value->complainant->offense->gravity->gravity == "Minor")) {
                $exceeded = 12;
                $tat = $value->ageing - $exceeded;
    
                if ($tat > 0) {
                    $tat_status = 'Exceeded TAT';
                }
            } else {
                $tat_status = 'Within TAT';
            }

            $value['days_exceeded_tat'] = $nteDaClosed->days_exceeded_tat ? 
            $nteDaClosed->days_exceeded_tat : $tat;
            $value['tat_status'] = $nteDaClosed->tat_status ? $nteDaClosed->tat_status : $tat_status;

            $year = Carbon::parse($value->created_at)->format('Y');
            $month = Carbon::parse($value->created_at)->formatLocalized('%b');
            $week = "WE " . Carbon::parse($value->created_at)->startOfWeek()->format('M-d')
            . " - " . Carbon::parse($value->created_at)->endOfWeek()->format('M-d');

            $value['year_nte_draft'] = $nteDaClosed->year_nte_draft ? 
            $nteDaClosed->year_nte_draft: $year;

            $value['month_nte_draft'] = $nteDaClosed->month_nte_draft ? 
            $nteDaClosed->month_nte_draft : $month;

            $value['week'] = $nteDaClosed->week ? $nteDaClosed->week : $week;

            /* Start Quarter */
            $quarter = '';
            if ($month == 'January' || $month == 'February' || $month == 'March') {
                $quarter = 'Q1';
            } elseif ($month == 'April' || $month == 'May' || $month == 'June') {
                $quarter = 'Q2';
            } elseif ($month == 'July' || $month == 'August' || $month == 'September') {
                $quarter = 'Q3';
            } elseif ($month == 'October' || $month == 'November' || $month == 'December') {
                $quarter = 'Q4';
            }

            $value['quarter'] = $nteDaClosed->quarter ? $nteDaClosed->quarter : $quarter;

                    /* Start RANK */
        if (Str::contains($value->respondent_user->designation->name, 'Specialist') == true
                || Str::contains($value->respondent_user->designation->name, 'Analyst') == true
                || Str::contains($value->respondent_user->designation->name, 'SME') == true
                || Str::contains($value->respondent_user->designation->name, 'Mentor') == true
                || Str::contains($value->respondent_user->designation->name, 'Trainer') == true
                || Str::contains($value->respondent_user->designation->name, 'HR') == true
                || Str::contains($value->respondent_user->designation->name, 'Manager') == true
                || Str::contains($value->respondent_user->designation->name, 'Collection') == true
            ) {
                $rank = 'Ops Support';
            } elseif (Str::contains($value->respondent_user->designation->name, 'Designer') == true
                || Str::contains($value->respondent_user->designation->name, 'Support') == true
                || Str::contains($value->respondent_user->designation->name, 'Programmer') == true
                || Str::contains($value->respondent_user->designation->name, 'Developer') == true
                || Str::contains($value->respondent_user->designation->name, 'IT') == true
                || Str::contains($value->respondent_user->designation->name, 'Accountant') == true
                || Str::contains($value->respondent_user->designation->name, 'Compensation and Benefit') == true
            ) {
                $rank = 'Shared Services';
            } elseif (Str::contains($value->respondent_user->designation->name, 'Supervisor') == true
            || Str::contains($value->respondent_user->designation->name, 'Team Lead') == true) {
                $rank = 'Supervisor';
            } else {
                $rank = 'Agent';
            }

            $value['rank'] = $nteDaClosed->rank ? $nteDaClosed->rank : $rank;

            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);
        return $data_to_collection;
    }

}
