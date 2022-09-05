<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App;
use Excel;
use Exception;
use App\Models\{
    User,
    HrbpCluster,
    OfficeLocation,
    NteDaClosedStatus
};

class UsersController extends Controller
{
    public function index(): JsonResponse
    {
        $user = User::all();
        return response()->json($user);
    }

    public function dropdown()
    {
        $user = User::select('id as value', DB::raw('CONCAT(TRIM(last_name), " ", first_name) AS text'))
                ->where('id', '!=', Auth()->id())
                ->orderByRaw('TRIM(last_name)')
                ->get();

        return response()->json($user);
    }

    public function dropdownEmail()
    {
        $user = User::select('id as value',
            DB::raw('CONCAT(TRIM(last_name), " ", first_name, " - ", email_address) AS text'));
        $user = $user->whereNotNull('email_address');
        $user = $user->orderByRaw('TRIM(last_name)');
        $user = $user->get();

        return response()->json($user);
    }

    public function dropdownEmployeeWithHrbp()
    {
        $hrbps = HrbpCluster::with(['employee_current', 'cluster_alignment', 'hrbp_user']);
        $hrbps = $hrbps->whereNotNull('hrbp_employee_no');
        $hrbps = $hrbps->get();

        $getValue = [];
        foreach ($hrbps as $hrbp) {
            if ($hrbp->employee_current) {
                $id_users = $hrbp->employee_current->id;
                $first_users = $hrbp->employee_current->first_name;
                $last_users = $hrbp->employee_current->last_name;
                $employee_nos = $hrbp->employee_current->employee_no;

                $first_hrbp = $hrbp->hrbp_user->first_name;
                $last_hrbp = $hrbp->hrbp_user->last_name;

                    $getValue[]  = array(
                        'value' => $id_users,
                        'text' => $first_users." ". $last_users." (" . $employee_nos . ") - HRBP: "
                        . $first_hrbp." ". $last_hrbp
                    );
            }
        }

        $hrbps = $getValue;

        return $hrbps;
    }

    public function supervisorManagerAccess(): JsonResponse
    {
        $user_emp_no = auth()->user()->employee_no;

        $head = User::with(['designation'])
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Manager%');
            $query->orWhere('name', 'LIKE', '%Supervisor%');
            $query->orWhere('name', 'LIKE', '%Team%');
            $query->orWhere('name', 'LIKE', '%Lead%');
            $query->where('name', 'NOT LIKE', '%HR%');
        })
        ->where('employee_no', $user_emp_no)
        ->get();

        return response()->json($head);
    }

    public function dropdownVpsOfficeLocation()
    {
        $vpsSiteLoc = OfficeLocation::select('id as value', 'name AS text')
        ->whereIn('name', ['Alabang', 'Bacolod', 'Market Market', 'Two Neo'])
        ->get();

        return response()->json($vpsSiteLoc);
    }

    public static function UpdateVpsOfficeLocation($employee_no, $vps_office_location_id, 
    $vps_office_location_text)
    {
        User::where('employee_no', $employee_no)
        ->update(['work_location_id' => $vps_office_location_id]);

        /* update NteDaClosedStatus site*/
        NteDaClosedStatus::where('employee_id', $employee_no)
        ->update(['site' => $vps_office_location_text]);
    }

    public function importCsvSiteLocation(Request $request)
    {
        $path = $request->file('attachments')[0]->getRealPath();
        $valid = [];
        try {
            Excel::filter('chunk')->load($path)->chunk(200, function ($results) {
                foreach ($results as $key => $value) {
                    $validator = ValidatorController::VpsSiteLocationValidator($value);
                    if (count($value) != 2) {
                       throw new Exception('Excess/less column please check');
                    } elseif (!$validator->fails()) {
                        $site_name = OfficeLocation::where('name', 'LIKE', "%" . 
                        strtolower($value->vps_office_location) . "%")
                        ->get(['id'])->first();

                        User::where('employee_no', $value->employee_no)
                        ->update([
                            'work_location_id' => $site_name->id,
                        ]);

                        /* update NteDaClosedStatus site*/
                        NteDaClosedStatus::where('employee_id', $value->employee_no)
                        ->update(['site' => $value->vps_office_location]);
                    } else {
                        throw new Exception($validator->messages());
                    }
                }
            });
        } catch (Exception $exception) {
            $remove_char = str_replace(['}','{','"', ' selected'], "", $exception->getMessage());
            $remove_char_again_array = explode(',', $remove_char);
            $array_message = [];
            foreach ($remove_char_again_array as $value) {
                $remove_char_in_array = str_replace(['[',']'], "", $value);
                $message_array = explode(':', $remove_char_in_array);
                if ($message_array[1]) {
                    array_push($array_message, $message_array[1]. " Error occured in row number ". ($key+1));
                } elseif ($message_array[0]) {
                    array_push($array_message, $message_array[0]);
                }
            }

            return response()->json(['isvalid'=>false,'errors'=>$array_message]);
        }
        return response()->json(['isvalid'=>true]);
    }
}
