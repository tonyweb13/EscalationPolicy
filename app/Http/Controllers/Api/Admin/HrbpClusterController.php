<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ValidatorController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Excel;
use App\Models\{
    User,
    HrbpCluster,
    OfficeLocation,
    UsersGroup
};
use Illuminate\Http\JsonResponse;
use Mockery\Undefined;

class HrbpClusterController extends Controller
{
    public function index(): JsonResponse
    {
        $designation = auth()->user()->designation->name;
        $user_emp_no = auth()->user()->employee_no;
        $user_id = auth()->user()->id;
        $roles = auth()->user()->roles[0]->name;
        $work_location_id = auth()->user()->work_location_id;

        $hrbp = HrbpCluster::with([
            'hrsl_user',
            'hrbp_user',
            'site_office',
            'employee_current.designation',
            'cluster_alignment.supervisor_assign.employee_supervisor.supervisor_assign',
        ]);

        if ($roles == "HR Admin Access") {
            $hrbp = $hrbp->where('hrsl_employee_no', $user_emp_no)->get();
        } elseif ($roles == "HR Access") {
            $hrbp = $hrbp->where('hrbp_employee_no', $user_emp_no)
            ->orWhere('hrsl_office_location_id', $work_location_id)
            ->get();
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1) {
                $hrbp = $hrbp->orderBy('id', 'desc')->get();
            } elseif (Str::contains($designation, 'Resolution Manager') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }
                array_push($supArray, $user_emp_no);

                $hrbp = $hrbp->whereIn('user_employee_no', $supArray)
                    ->orderBy('hrbp_employee_no', 'desc')->get();
            } else {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['employee_no'];
                }
                array_push($supArray2, $user_id);

                $hrbp = $hrbp->whereIn('user_employee_no', $supArray2)
                    ->orderBy('id', 'desc')->get();
            }
        } elseif (strpos($designation, 'Supervisor') == true || strpos($designation, 'Team Lead') == true) {
            if (Str::contains($designation, 'Senior Operation Supervisor') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['employee_no'];
                }

                $supArray3 =  array_merge($supArray2, $supArray);

                $hrbp = $hrbp->whereIn('user_employee_no', $supArray3)->get();
            } else {
                $userSups = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                foreach ($userSups as $userSup) {
                    $supArray[] = $userSup['employee_assign']['employee_no'];
                }
                $hrbp = $hrbp->whereIn('user_employee_no', $supArray)->get();
            }
        } else {
            $hrbp = $hrbp->get();
        }
        $supervisor = [];
        foreach ($hrbp as $value) {
            if ($value->cluster_alignment && $value->cluster_alignment->supervisor_assign) {
                $value['supervisor'] = [
                    'first_name' => $value->cluster_alignment
                    ->supervisor_assign
                    ->first_name,
                    'last_name' => $value->cluster_alignment
                    ->supervisor_assign
                    ->last_name
                ];
            }

            if ($value->cluster_alignment && $value->cluster_alignment->supervisor_assign
            && $value->cluster_alignment->supervisor_assign->employee_supervisor
            && $value->cluster_alignment->supervisor_assign->employee_supervisor->supervisor_assign) {
                $value['dm'] = [
                    'first_name' => $value->cluster_alignment
                    ->supervisor_assign
                    ->employee_supervisor
                    ->supervisor_assign
                    ->first_name,
                    'last_name' => $value->cluster_alignment
                    ->supervisor_assign
                    ->employee_supervisor
                    ->supervisor_assign
                    ->last_name
                ];
            }

            if (empty($value->cluster_alignment->supervisor_assign)) {
                $value['supervisor'] = ['first_name' => '', 'last_name' => ''];
                if (empty($value->cluster_alignment->supervisor_assign->employee_supervisor->supervisor_assign)) {
                    $value['dm'] = ['first_name' => '', 'last_name' => ''];
                }
            }
            array_push($supervisor, $value);
        }

        $hrbp_data = new Collection;
        $hrbp_data = collect($supervisor);
        return response()->json($hrbp_data);
    }

    public function create(Request $request)
    {
        $user_employee_no = HrbpCluster::where('user_employee_no', $request->user_employee_no)->first();

        if (!$user_employee_no) {
            $hrbp = new HrbpCluster;
            $hrbp->hrsl_employee_no = $request->hrsl_employee_no;
            $hrbp->hrsl_email_address = $request->hrsl_email_address;
            $hrbp->hrbp_employee_no = $request->hrbp_employee_no;
            $hrbp->hrbp_email_address = $request->hrbp_email_address;
            $hrbp->hrsl_office_location_id = $request->hrsl_office_location_id;
            $hrbp->user_employee_no = $request->user_employee_no;
            $hrbp->save();

            if ($request->user_employee_no && $request->supervisor_employee_no) {
                $UsersGroup = new UsersGroup;
                $UsersGroup->employee_no = $request->user_employee_no;
                $UsersGroup->teamlead_employee_no = $request->supervisor_employee_no;
                $UsersGroup->save();
            }

            if ($request->supervisor_employee_no && $request->manager_employee_no) {
                $UsersGroup = new UsersGroup;
                $UsersGroup->employee_no = $request->supervisor_employee_no;
                $UsersGroup->teamlead_employee_no = $request->manager_employee_no;
                $UsersGroup->save();
            }

            return $hrbp;
        }
    }

    public function show(int $id, Request $request)
    {
        $hrbp = HrbpCluster::updateOrCreate(['id' => $id], $request->all());

        if ($request->user_employee_no && $request->supervisor_employee_no) {
            $notExistInGroup = UsersGroup::where('employee_no', $request->user_employee_no)->get();

            if ($notExistInGroup) {
                $UsersGroup = new UsersGroup;
                $UsersGroup->employee_no = $request->user_employee_no;
                $UsersGroup->teamlead_employee_no = $request->supervisor_employee_no;
                $UsersGroup->save();
            } else {
                $sup = UsersGroup::where('employee_no', $request->user_employee_no)
                ->update(['teamlead_employee_no' => $request->supervisor_employee_no]);
            }

            if ($sup && $request->manager_employee_no) {
                // echo "if sup manager_employee_no==" + $request->manager_employee_no;
                // exit(); 
                $manager = UsersGroup::where('employee_no', $request->supervisor_employee_no)
                ->update(['teamlead_employee_no' => $request->manager_employee_no]);

                return $manager;
            } else {
                if ($request->manager_employee_no == '' || $request->manager_employee_no == Undefined) {
                    UsersGroup::where('employee_no', $request->supervisor_employee_no)
                    ->where('teamlead_employee_no', 0)
                    ->orWhere('teamlead_employee_no', null)
                    ->delete();
                }

                return $sup;
            }
        } else {
            return $hrbp;
        }
    }

    public function edit(int $id)
    {
        $hrbp = HrbpCluster::all()->where('id', $id)->first();

        return $hrbp;
    }

    public function destroy(int $id)
    {
        $hrbp = HrbpCluster::findOrFail($id)->delete();

        return response()->json($hrbp);
    }

    public function dropdownSiteLocation()
    {
        $user = OfficeLocation::select('id as value', 'name AS text')
        ->whereNotIn('name', ['BGC', 'Netsquare', 'MARKET', 'Market-Market', 'MarketMarket', 'Market! Market!', 'BCD'])
        ->get();

        return response()->json($user);
    }

    public function getEmailAddress($employee_no)
    {
        $email = User::where('employee_no', $employee_no)->first(['email_address']);

        return response()->json($email);
    }

    public function dropdownHRBP()
    {
        $user = User::select('employee_no as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Business Partner%');
            $query->orWhere('name', 'LIKE', '%HRBP%');
            $query->orWhere('name', 'LIKE', '%HR Site Lead%');
        })->get();

        return response()->json($user);
    }

    public function dropdownHRBPbyId()
    {
        $user = User::select('id as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Business Partner%');
            $query->orWhere('name', 'LIKE', '%HRBP%');
            $query->orWhere('name', 'LIKE', '%HR Site Lead%');
        })->get();

        return response()->json($user);
    }

    public function dropdownHRBPbyName()
    {
        $user = User::select(DB::raw('CONCAT(first_name, " ", last_name) AS value'),
        DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Business Partner%');
            $query->orWhere('name', 'LIKE', '%HRBP%');
            $query->orWhere('name', 'LIKE', '%HR Site Lead%');
        })->get();

        return response()->json($user);
    }

    public function dropdownHrSiteLeader()
    {
        $user = User::select('employee_no as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
            ->whereHas('designation', function ($query) {
                $query->where('name', 'LIKE', '%HR Site%');
            })->get();

        return response()->json($user);
    }

    public function dropdownHeadOperation()
    {
        $user = User::select('employee_no as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereHas('designation', function ($query) {
            $query->where('name', 'LIKE', '%Supervisor%');
            $query->orWhere('name', 'LIKE', '%Lead%');
            $query->orWhere('name', 'LIKE', '%Mentor%');
            $query->orWhere('name', 'LIKE', '%Admin%');
            $query->orWhere('name', 'LIKE', '%Senior%');
            $query->orWhere('name', 'LIKE', '%Sr%');
            $query->orWhere('name', 'LIKE', '%Manage%');
            $query->orWhere('name', 'LIKE', '%Head%');
            $query->orWhere('name', 'LIKE', '%Chief%');
            $query->orWhere('name', 'LIKE', '%Director%');
        })->get();

        return response()->json($user);
    }

    public function dropdownNotExistGroup()
    {
        $user_groups = UsersGroup::get(['employee_no']);

        $group_emp_no = [];
        foreach ($user_groups as $user_group) {
            $group_emp_no[] = $user_group['employee_no'];
        }

        $user_emp_no = User::select('employee_no as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereNotIn('employee_no', $group_emp_no)
        ->get();

        return response()->json($user_emp_no);
    }
    
    public function dropdownNotExistGroupAndCluster()
    {
        $hrbp_clusters = HrbpCluster::get(['user_employee_no']);

        $cluster_emp_no = [];
        foreach ($hrbp_clusters as $hrbp_cluster) {
            $cluster_emp_no[] = $hrbp_cluster['user_employee_no'];
        }

        $user_groups = UsersGroup::get(['employee_no']);

        $group_emp_no = [];
        foreach ($user_groups as $user_group) {
            $group_emp_no[] = $user_group['employee_no'];
        }

        $user_emp_no = User::select('employee_no as value', DB::raw('CONCAT(first_name, " ", last_name) AS text'))
        ->whereNotIn('employee_no', $cluster_emp_no)
        ->whereNotIn('employee_no', $group_emp_no)
        ->orWhereNotIn('employee_no', $cluster_emp_no)
        ->get();

        return response()->json($user_emp_no);
    }

    public function findHrsl($employee_no)
    {
        $hrsl = User::where('employee_no', $employee_no)->get(['first_name', 'last_name']);
        return $hrsl;
    }

    public function importCsv(Request $request)
    {
        $path = $request->file('attachments')[0]->getRealPath();
        $valid = [];
        try {
            Excel::filter('chunk')->load($path)->chunk(200, function ($results) {
                foreach ($results as $key => $value) {
                    $validator = ValidatorController::hrbpClusterValidator($value);
                    if (count($value) != 8) {
                       throw new Exception('Excess/less column please check');
                    } elseif (!$validator->fails()) {
                        $hrsl_location = OfficeLocation::where('name', 'LIKE', "%".strtolower($value->hrsl_location)."%")
                        ->get(['id'])->first();

                        $existHrbpEmployee = HrbpCluster::where('user_employee_no', $value->user_employee_no)->first();

                        if ($existHrbpEmployee->user_employee_no) {
                            HrbpCluster::where('user_employee_no', $value->user_employee_no)
                            ->update([
                                'hrsl_employee_no' => $value->hrsl_employee_no,
                                'hrsl_email_address' => $value->hrsl_email_address,
                                'hrbp_employee_no' => $value->hrbp_employee_no,
                                'hrbp_email_address' => $value->hrbp_email_address,
                                'hrsl_office_location_id' => $hrsl_location->id,
                            ]);
                        } else {
                            HrbpCluster::firstOrCreate(
                                [
                                'hrsl_employee_no' => $value->hrsl_employee_no,
                                'hrsl_email_address' => $value->hrsl_email_address,
                                'hrbp_employee_no' => $value->hrbp_employee_no,
                                'hrbp_email_address' => $value->hrbp_email_address,
                                'hrsl_office_location_id' => $hrsl_location->id,
                                'user_employee_no' => $value->user_employee_no,
                                ]
                            );
                        }

                        $existGroupEmployee = UsersGroup::where('employee_no', $value->user_employee_no)->first();

                        if ($existGroupEmployee->employee_no) {
                            UsersGroup::where('employee_no', $value->user_employee_no)
                            ->update(['teamlead_employee_no' => $value->supervisor_employee_no]);
                        } else {
                            UsersGroup::firstOrCreate(
                                [
                                'employee_no' => $value->user_employee_no,
                                'teamlead_employee_no' => $value->supervisor_employee_no,
                                ]
                            );
                        }

                        $existGroupSupervisor = UsersGroup::where('employee_no', $value->supervisor_employee_no)->first();

                        if ($value->supervisor_employee_no && $value->manager_employee_no &&
                            $existGroupSupervisor->employee_no) {
                            UsersGroup::where('employee_no', $value->supervisor_employee_no)
                            ->update([ 'teamlead_employee_no' => $value->manager_employee_no]);
                        } else {
                            UsersGroup::firstOrCreate(
                                [
                                'employee_no' => $value->supervisor_employee_no,
                                'teamlead_employee_no' => $value->manager_employee_no,
                                ]
                            );
                        }
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
