<?php

namespace App\Http\Controllers\Api\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use App\Models\{Respondent, User, UsersGroup};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardCountController extends Controller
{
    public function initialQuery($user_id, $role, $date)
    {
        $respondent = Respondent::select('status_id', 'respondent_user_id', 'ir_id')
            ->whereDate('updated_at', $date)
            ->with(['incident_report' => function ($query) {
                $query->where('is_generate_nte_invalid_ir', '!=', 2);
            }]);

        if ($role === 'Regular User Access') {
            $respondent = $respondent->where('respondent_user_id', $user_id)->get();
        } elseif ($role === 'Regular Supervisor Access') {
            $user = User::find($user_id);
            $respondent = $respondent->with([
                'respondent_user.employee_supervisor' => function ($query) use ($user) {
                    $query->where('teamlead_employee_no', '=', $user->employee_no);
                }])->get();
            $collection = new Collection;
            $data = [];
            foreach ($respondent as $value) {
                if ($value->respondent_user->employee_supervisor != null) {
                    array_push($data, $value);
                }
            }
            $collection = collect($data);
            $respondent = $collection;
        } elseif ($role === 'HR Access' || $role === 'HR Admin Access') {
            $user = User::with('office_location')->find($user_id);
            $respondent->with([
                'respondent_user.employee_supervisor.employee_assign.office_location' => function ($query) use ($user) {
                    $query->where('name', '=', $user->office_location->name);
                }]);
            $respondent = $respondent->get();
            $collection = new Collection;
            $data = [];
            foreach ($respondent as $value) {
                if ($value->respondent_user->employee_supervisor != null) {
                    array_push($data, $value);
                }
            }
            $collection = collect($data);
            $respondent = $collection;
        } elseif ($role === 'Super Admin Access') {
            $respondent = $respondent->get();
        }

        return $respondent;
    }

    public function invalidQuery($respondent)
    {
        $collection = new Collection;
        $invalid = [];
        foreach ($respondent as $value) {
            if ($value->incident_report != null) {
                if ($value->incident_report->is_generate_nte_invalid_ir == 2) {
                    array_push($invalid, $value);
                }
            }
        }
        $collection = new Collection;
        $collection = collect($invalid);
        $invalid = $collection->count();

        return $invalid;
    }

    public function getDashboardHeaders(Request $request): JsonResponse
    {
        $date = Carbon::now();
        $emp_id = auth()->user()->id;
        $user = User::where('id', $emp_id)->with('roles')->first();
        $initial_data = $this->initialQuery($emp_id, $user->roles[0]->name, $date->toDateString());
        $dashboard_headers = [
            'open' => $initial_data->whereIn('status_id', [1,2,3])->count(),
            'close' => $initial_data->where('status_id', 4)->count(),
            'invalid' => $this->invalidQuery($initial_data),
            'total' => $initial_data->count()
        ];

        return response()->json($dashboard_headers);
    }

    public function countOpenClosed(string $status, string $getMonth)
    {
        $open = [1, 2, 3];
        $closed = 4;

        if ($status == "open") {
            $respondent_user_ids = Respondent::whereIn('status_id', $open);
        } else if ($status == "closed") {
            $respondent_user_ids = Respondent::where('status_id', $closed);
        }

        if ($getMonth == "lastmonth") {
            /* get last Month */
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();

            $respondent_user_ids = $respondent_user_ids->whereBetween('created_at', [$start, $end])->pluck('respondent_user_id');

        } elseif ($getMonth == "currentmonth") {
            /* get current Month */
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->toDateTimeString();

            $respondent_user_ids = $respondent_user_ids->whereBetween('created_at', [$start, $end])->pluck('respondent_user_id');
        }

        /* count for Alabang */
        $alabang = User::whereIn('id', $respondent_user_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Alabang%');
        })->count();

        /* count for Bacolod */
        $bacolod = User::whereIn('id', $respondent_user_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Bacolod%');
            $query->orWhere('name', 'LIKE', '%BCD%');
        })->count();

        /* count for Market Market */
        $market = User::with(['office_location'])->whereIn('id', $respondent_user_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Market%');
        })->count();

        /* count for Net Square */
        $bgc = User::whereIn('id', $respondent_user_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Net%');
            $query->orWhere('name', 'LIKE', '%Neo%');
            $query->orWhere('name', 'LIKE', '%BGC%');
        })->count();

        return [$alabang, $bacolod, $market, $bgc];
    }

    // public function countOpenClosed(string $status, string $getMonth)
    // {
    //     if ($status == "open") {
    //         $getStatus = '1, 2, 3';
    //     } else if ($status == "closed") {
    //         $getStatus = '4';
    //     }

    //     if ($getMonth == "lastmonth") {
    //         /* get last Month */
    //         $start = Carbon::now()->subMonth()->startOfMonth();
    //         $end = Carbon::now()->subMonth()->endOfMonth();
    //         $getColumnDate = 'created_at';

    //     } elseif ($getMonth == "currentmonth") {
    //         /* get current Month */
    //         $start = Carbon::now()->startOfMonth();
    //         $end = Carbon::now()->toDateTimeString();
    //         $getColumnDate = 'created_at';
    //     }

    //     $selectAlabang = DB::select('select count(*) as count_alabang from `respondents`
    //         left join '.env('DB_DATABASE_VPS').'.users
    //         on respondents.respondent_user_id = users.id
    //         left join '.env('DB_DATABASE_VPS').'.office_locations
    //         on users.work_location_id = office_locations.id
    //         where `status_id` in ('.$getStatus.')
    //         and  `respondents`.'.$getColumnDate.' between "'.$start.'" and "'.$end.'"
    //         and office_locations.name LIKE "%Alabang%"
    //     ');

    //     $selectBacolod = DB::select('select count(*) as count_bacolod from `respondents`
    //         left join '.env('DB_DATABASE_VPS').'.users
    //         on respondents.respondent_user_id = users.id
    //         left join '.env('DB_DATABASE_VPS').'.office_locations
    //         on users.work_location_id = office_locations.id
    //         where `status_id` in ('.$getStatus.')
    //         and  `respondents`.'.$getColumnDate.' between "'.$start.'" and "'.$end.'"
    //         and office_locations.name LIKE "%Bacolod%"
    //         or office_locations.name LIKE "%BCD%"
    //     ');

    //     $selectMarket = DB::select('select count(*) as count_market from `respondents`
    //         left join '.env('DB_DATABASE_VPS').'.users
    //         on respondents.respondent_user_id = users.id
    //         left join '.env('DB_DATABASE_VPS').'.office_locations
    //         on users.work_location_id = office_locations.id
    //         where `status_id` in ('.$getStatus.')
    //         and  `respondents`.'.$getColumnDate.' between "'.$start.'" and "'.$end.'"
    //         and office_locations.name LIKE "%Market%"
    //     ');

    //     $selectBgc = DB::select('select count(*) as count_bgc from `respondents`
    //         left join '.env('DB_DATABASE_VPS').'.users
    //         on respondents.respondent_user_id = users.id
    //         left join '.env('DB_DATABASE_VPS').'.office_locations
    //         on users.work_location_id = office_locations.id
    //         where `status_id` in ('.$getStatus.')
    //         and  `respondents`.'.$getColumnDate.' between "'.$start.'" and "'.$end.'"
    //         and office_locations.name LIKE "%Net%"
    //         or office_locations.name LIKE "%Neo%"
    //         or office_locations.name LIKE "%BGC%"
    //     ');

    //     $return_count = [];
    //     foreach($selectAlabang as $alabang){
    //         array_push($return_count, $alabang->count_alabang);
    //     }

    //     foreach($selectBacolod as $bacolod){
    //         array_push($return_count, $bacolod->count_bacolod);
    //     }

    //     foreach($selectMarket as $market){
    //         array_push($return_count, $market->count_market);
    //     }

    //     foreach($selectBgc as $bgc){
    //         array_push($return_count, $bgc->count_bgc);
    //     }

    //     return $return_count;
    // }

    public function countWithinOverTAT(string $WithinOver, string $getMonth)
    {
        if ($getMonth == "lastmonth") {
            /* get last Month */
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();

            $respondent_ids = Respondent::with(['complainant.offense.gravity'])->where('status_id', 4)->whereBetween('created_at', [$start, $end]);

        } elseif ($getMonth == "currentmonth") {
            /* get current Month */
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->toDateTimeString();

            $respondent_ids = Respondent::with(['complainant.offense.gravity'])
            ->whereIn('status_id', [1,2,3,4])
            ->whereBetween('created_at', [$start, $end]);
            // ->whereBetween('updated_at', [$start, $end]);
        }

        if ($WithinOver == "within") {
            $respondent_ids = $respondent_ids->where('ageing', '<', 12);
            $respondent_ids = $respondent_ids->orWhereHas('complainant.offense.gravity', function ($query) {
                    $query->whereIn('gravity',  ['Serious', 'Grave']);
                });
            $respondent_ids = $respondent_ids->where('ageing', '>=', 12)->where('ageing', '<', 16);

        } else if ($WithinOver == "over") {
            $respondent_ids = $respondent_ids->where('ageing', '>=', 16);
        }

        $respondent_ids = $respondent_ids->pluck('respondent_user_id');

        /* count for Alabang */
        $alabang = User::whereIn('id', $respondent_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Alabang%');
        })->count();

        /* count for Bacolod */
        $bacolod = User::whereIn('id', $respondent_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Bacolod%');
            $query->orWhere('name', 'LIKE', '%BCD%');
        })->count();

        /* count for Market Market */
        $market = User::with(['office_location'])->whereIn('id', $respondent_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Market%');
        })->count();

        /* count for Net Square */
        $bgc = User::whereIn('id', $respondent_ids)
        ->whereHas('office_location', function ($query) {
            $query->where('name', 'LIKE', '%Net%');
            $query->orWhere('name', 'LIKE', '%Neo%');
            $query->orWhere('name', 'LIKE', '%BGC%');
        })->count();

        return [$alabang, $bacolod, $market, $bgc];
    }

    public function countYTD(string $status)
    {
        /* Total Cases Count
        *  previous 6 months
        * (Closed/Open/Stage of the Case Not Closed)
        * filter by Month and year, open&closed, DA imposed <> "absolved"
        */

        $start7 = Carbon::now()->subMonths(7)->startOfMonth();
        $end7 = Carbon::now()->subMonths(7)->endOfMonth();

        $monthSeven = Respondent::whereBetween('created_at', [$start7, $end7])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $start6 = Carbon::now()->subMonths(6)->startOfMonth();
        $end6 = Carbon::now()->subMonths(6)->endOfMonth();

        $monthSix = Respondent::whereBetween('created_at', [$start6, $end6])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        /* Inc+/ Dec-
        * Get Increase/decrease average
        * ((count(prev month)/count(get month))-1)*100
        */
        $average6 = number_format((float)(($monthSix/$monthSeven)-1)*100, 2, '.', '');

        $start5 = Carbon::now()->subMonths(5)->startOfMonth();
        $end5 = Carbon::now()->subMonths(5)->endOfMonth();

        $monthFive = Respondent::whereBetween('created_at', [$start5, $end5])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $average5 = number_format((float)(($monthFive/$monthSix)-1)*100, 2, '.', '');

        $start4 = Carbon::now()->subMonths(4)->startOfMonth();
        $end4 = Carbon::now()->subMonths(4)->endOfMonth();

        $monthFourth = Respondent::whereBetween('created_at', [$start4, $end4])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $average4 = number_format((float)(($monthFourth/$monthFive)-1)*100, 2, '.', '');

        $start3 = Carbon::now()->subMonths(3)->startOfMonth();
        $end3 = Carbon::now()->subMonths(3)->endOfMonth();

        $monthThird = Respondent::whereBetween('created_at', [$start3, $end3])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $average3 = number_format((float)(($monthThird/$monthFourth)-1)*100, 2, '.', '');

        $start2 = Carbon::now()->subMonths(2)->startOfMonth();
        $end2 = Carbon::now()->subMonths(2)->endOfMonth();

        $monthSecond = Respondent::whereBetween('created_at', [$start2, $end2])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $average2 = number_format((float)(($monthSecond/$monthThird)-1)*100, 2, '.', '');

        $start1 = Carbon::now()->subMonths(1)->startOfMonth();
        $end1 = Carbon::now()->subMonths(1)->endOfMonth();

        $monthFirst = Respondent::whereBetween('created_at', [$start1, $end1])
        ->whereHas('incident_report.irr', function ($query) {
            $query->where('name', 'NOT LIKE', '%Absolve%');
        })->count();

        $average1 = number_format((float)(($monthFirst/$monthSecond)-1)*100, 2, '.', '');

        if ($status == "totalcases") {
            return [$monthSix, $monthFive, $monthFourth, $monthThird, $monthSecond, $monthFirst];

        } else if ($status == "incdec") {
            return [$average6, $average5, $average4, $average3, $average2, $average1];
        }
    }
}
