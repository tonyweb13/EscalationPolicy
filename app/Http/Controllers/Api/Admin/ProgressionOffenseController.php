<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Models\{
    ProgressionOffense,
    ProgressionOccurrence,
    UsersGroup,
    User,
    HrbpCluster,
    NteDaClosedStatus,
    Respondent
};
use App\Models\Admin\Settings\Coc\{Offense, AttendancePointsSystem};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressionOffenseController extends Controller
{
    public function attendanceOffense() : JsonResponse
    {
        $user = User::where('employee_no', auth()->user()->employee_no)->with([
            'roles',
            'office_location',
            'designation'
        ])->first();
        $offense_list = AttendancePointsSystem::where('category_id', 15)->get();
        $array_offense_id = [];
        foreach ($offense_list as $value) {
            array_push($array_offense_id, $value->id);
        }
        $progression_occurence = ProgressionOccurrence::with([
            'attendancepointssystem',
            'employee_user',
            //first occurrence
            'first_respondent.incident_report.hrbp_user',
            'first_respondent.complainant.attendancepointssystem.category',
            'first_respondent.complainant.attendancepointssystem.attendancepoint',
            'first_respondent.complainant.complainant_user',
            'first_respondent.incident_report.witness_user.witness_fullname',
            'first_respondent.incident_report.invite_user.invite_fullname',

            //second occurrence
            'second_respondent.incident_report.hrbp_user',
            'second_respondent.complainant.attendancepointssystem.category',
            'second_respondent.complainant.attendancepointssystem.attendancepoint',
            'second_respondent.complainant.complainant_user',
            'second_respondent.incident_report.witness_user.witness_fullname',
            'second_respondent.incident_report.invite_user.invite_fullname',

            //third occurrence
            'third_respondent.incident_report.hrbp_user',
            'third_respondent.complainant.attendancepointssystem.category',
            'third_respondent.complainant.attendancepointssystem.attendancepoint',
            'third_respondent.complainant.complainant_user',
            'third_respondent.incident_report.witness_user.witness_fullname',
            'third_respondent.incident_report.invite_user.invite_fullname',

            //fourth occurrence
            'fourth_respondent.incident_report.hrbp_user',
            'fourth_respondent.complainant.attendancepointssystem.category',
            'fourth_respondent.complainant.attendancepointssystem.attendancepoint',
            'fourth_respondent.complainant.complainant_user',
            'fourth_respondent.incident_report.witness_user.witness_fullname',
            'fourth_respondent.incident_report.invite_user.invite_fullname',

            //fifth occurrence
            'fifth_respondent.incident_report.hrbp_user',
            'fifth_respondent.complainant.attendancepointssystem.category',
            'fifth_respondent.complainant.attendancepointssystem.attendancepoint',
            'fifth_respondent.complainant.complainant_user',
            'fifth_respondent.incident_report.witness_user.witness_fullname',
            'fifth_respondent.incident_report.invite_user.invite_fullname',

            //sixth occurrence
            'sixth_respondent.incident_report.hrbp_user',
            'sixth_respondent.complainant.attendancepointssystem.category',
            'sixth_respondent.complainant.attendancepointssystem.attendancepoint',
            'sixth_respondent.complainant.complainant_user',
            'sixth_respondent.incident_report.witness_user.witness_fullname',
            'sixth_respondent.incident_report.invite_user.invite_fullname',

            //seventh occurrence
            'seventh_respondent.incident_report.hrbp_user',
            'seventh_respondent.complainant.attendancepointssystem.category',
            'seventh_respondent.complainant.attendancepointssystem.attendancepoint',
            'seventh_respondent.complainant.complainant_user',
            'seventh_respondent.incident_report.witness_user.witness_fullname',
            'seventh_respondent.incident_report.invite_user.invite_fullname'
        ])->whereIn('attendance_points_system_id', $array_offense_id);

        $progression = $this->userDistinct($user, $progression_occurence);
        $progression = $progression->orderBy('employee_id');
        $progression = $progression->get();

        return response()->json($progression);
    }

    public function progressionOffense($category_id) : JsonResponse
    {
        $user = User::where('employee_no', auth()->user()->employee_no)->with([
            'roles',
            'office_location',
            'designation'
        ])->first();
        $offense_list = Offense::withTrashed()->where('category_id', $category_id)->get();
        $array_offense_id = [];
        foreach ($offense_list as $value) {
            array_push($array_offense_id, $value->id);
        }

        ini_set('memory_limit', '4095M');
        $progression_occurence = ProgressionOccurrence::with([
            'employee_user',
            'offense',
            'offense.gravity',
            'offense.category',

            //first occurrence
            'first_respondent.incident_report.hrbp_user',
            'first_respondent.complainant.offense.gravity',
            'first_respondent.complainant.offense.category',
            'first_respondent.complainant.complainant_user',
            'first_respondent.incident_report.witness_user.witness_fullname',
            'first_respondent.incident_report.invite_user.invite_fullname',
            'first_respondent.incident_report.irr',

            //second occurrence
            'second_respondent.incident_report.hrbp_user',
            'second_respondent.complainant.offense.gravity',
            'second_respondent.complainant.offense.category',
            'second_respondent.complainant.complainant_user',
            'second_respondent.incident_report.witness_user.witness_fullname',
            'second_respondent.incident_report.invite_user.invite_fullname',
            'second_respondent.incident_report.irr',

            //third occurrence
            'third_respondent.incident_report.hrbp_user',
            'third_respondent.complainant.offense.gravity',
            'third_respondent.complainant.offense.category',
            'third_respondent.complainant.complainant_user',
            'third_respondent.incident_report.witness_user.witness_fullname',
            'third_respondent.incident_report.invite_user.invite_fullname',
            'third_respondent.incident_report.irr',

            //fourth occurrence
            'fourth_respondent.incident_report.hrbp_user',
            'fourth_respondent.complainant.offense.gravity',
            'fourth_respondent.complainant.offense.category',
            'fourth_respondent.complainant.complainant_user',
            'fourth_respondent.incident_report.witness_user.witness_fullname',
            'fourth_respondent.incident_report.invite_user.invite_fullname',
            'fourth_respondent.incident_report.irr',

            //fifth occurrence
            'fifth_respondent.incident_report.hrbp_user',
            'fifth_respondent.complainant.offense.gravity',
            'fifth_respondent.complainant.offense.category',
            'fifth_respondent.complainant.complainant_user',
            'fifth_respondent.incident_report.witness_user.witness_fullname',
            'fifth_respondent.incident_report.invite_user.invite_fullname',
            'fifth_respondent.incident_report.irr',

            //sixth occurrence
            'sixth_respondent.incident_report.hrbp_user',
            'sixth_respondent.complainant.offense.gravity',
            'sixth_respondent.complainant.offense.category',
            'sixth_respondent.complainant.complainant_user',
            'sixth_respondent.incident_report.witness_user.witness_fullname',
            'sixth_respondent.incident_report.invite_user.invite_fullname',
            'sixth_respondent.incident_report.irr',

            //seventh occurrence
            'seventh_respondent.incident_report.hrbp_user',
            'seventh_respondent.complainant.offense.gravity',
            'seventh_respondent.complainant.offense.category',
            'seventh_respondent.complainant.complainant_user',
            'seventh_respondent.incident_report.witness_user.witness_fullname',
            'seventh_respondent.incident_report.invite_user.invite_fullname',
            'seventh_respondent.incident_report.irr'
        ])->whereIn('offense_id', $array_offense_id);

        $progression = $this->userDistinct($user, $progression_occurence);
        $progression = $progression->orderBy('employee_id');
        $progression = $progression->get();
        $progression = $this->archived($progression);

        return response()->json($progression);
    }

    private function archived($progression_occurence)
    {
        foreach ($progression_occurence as $value) {
            if ($value->offense->deleted_at) {
                $value->offense['offense_archived'] = 'Offense Already Archived';
            }

            if ($value->first_respondent->incident_report->irr->deleted_at) {
                $value->first_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->second_respondent->incident_report->irr->deleted_at) {
                $value->second_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->third_respondent->incident_report->irr->deleted_at) {
                $value->first_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->fourth_respondent->incident_report->irr->deleted_at) {
                $value->fourth_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->fifth_respondent->incident_report->irr->deleted_at) {
                $value->fifth_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->sixth_respondent->incident_report->irr->deleted_at) {
                $value->sixth_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }

            if ($value->seventh_respondent->incident_report->irr->deleted_at) {
                $value->seventh_respondent['irr_archived'] = 'Corrective Action Already Archived';
            }
        }

        return $progression_occurence;
    }

    private function userDistinct($user, $progression_occurence)
    {
        $user_employee_number = auth()->user()->employee_no;

        if ($user->roles[0]->name === 'HR Admin Access') {
            $site_user = User::where('work_location_id', $user->office_location->id)->select('id')->get();
            $array_site_employee_id = [];
            foreach ($site_user as $value) {
                array_push($array_site_employee_id, $value->id);
            }
            $progression_occurence->whereIn('employee_id', $array_site_employee_id);
        } elseif ($user->roles[0]->name === 'HR Access') {
            $hrbp = HrbpCluster::where('user_employee_no', $user->employee_no)->first();
            $users_under_hrbp = HrbpCluster::where('hrbp_employee_no', $hrbp->hrbp_employee_no)->get();
            $array_hrbp_under_employee_id = [];
            $array_employee_id = [];
            foreach ($users_under_hrbp as $value) {
                array_push($array_hrbp_under_employee_id, $value->user_employee_no);
            }
            $user = User::whereIn('employee_no', $array_hrbp_under_employee_id)->get();
            foreach ($user as $value) {
                array_push($array_employee_id, $value->id);
            }
            $progression_occurence->whereIn('employee_id', $array_employee_id);
        } elseif ($user->roles[0]->name == "Regular Supervisor Access" &&
            Str::contains($user->designation->name, 'Manager') == 1) {
            if (Str::contains($user->designation->name, 'Resolution Manager') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user->employee_no)
                    ->with('employee_assign')->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    array_push($supArray, $ugFirst->employee_assign->id);
                }

                $progression_occurence = $progression_occurence->whereIn('employee_id', $supArray);
            } elseif (Str::contains($user->designation->name, 'Senior Operations Manager') == 1) {
                $progression_occurence = $progression_occurence->orderBy('id', 'desc');
            } else {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_employee_number);

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray);

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['id'];
                }

                $progression_occurence = $progression_occurence->whereIn('employee_id', $supArray2)
                ->orderBy('id', 'desc');
            }
        } elseif ($user->roles[0]->name == "Regular Supervisor Access" &&
            Str::contains($user->designation->name, 'Supervisor') == 1 ||
            Str::contains($user->designation->name, 'Team Lead') == 1) {
            if (Str::contains($user->designation->name, 'Senior Operation Supervisor') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_employee_number)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                    $supArray1[] = $ugFirst['employee_assign']['id'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['id'];
                }

                $supArray3 =  array_merge($supArray2, $supArray1);

                $progression_occurence = $progression_occurence->whereIn('employee_id', $supArray3)
                ->orderBy('id', 'desc');
            } else {
                $userSups = UsersGroup::where('teamlead_employee_no', $user_employee_number)->get();

                $supArray = [auth()->id()];
                foreach ($userSups as $userSup) {
                    $supArray[] = $userSup['employee_assign']['id'];
                }

                $progression_occurence = $progression_occurence->whereIn('employee_id', $supArray)
                ->orderBy('id', 'desc');
            }
        } elseif ($user->roles[0]->name === 'Regular User Access') {
            $progression_occurence->where('employee_id', $user->id);
        } elseif ($user->roles[0]->name === 'Super Admin Access') {
            $progression_occurence = $progression_occurence;
        } else {
            $progression_occurence->where('employee_id', $user->id);
        }

        return $progression_occurence;
    }

    public function employeeProgressionOffense(string $employeeId, int $categoryId, int $respondentId)
    {
        $occurrence = ProgressionOccurrence::where('employee_id', $employeeId)->first();
        // echo "count(occurrence) ==" . count($occurrence) . "<br>";

        if (count($occurrence) > 0 && $occurrence->first_respondent_id) {
            // echo "update first_respondent_id ==".  $occurrence->first_respondent_id . "<br>";

            //update occurrence
            if (empty($occurrence->second_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                    ->update([
                        'second_respondent_id' => $respondentId,
                    ]);
            } elseif (empty($occurrence->third_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                    ->update([
                        'third_respondent_id' => $respondentId,
                    ]);
            } elseif (empty($occurrence->fourth_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                    ->update([
                        'fourth_respondent_id' => $respondentId,
                    ]);
            } elseif (empty($occurrence->fifth_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                ->update([
                    'fifth_respondent_id' => $respondentId,
                ]);
            } elseif (empty($occurrence->sixth_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                ->update([
                    'sixth_respondent_id' => $respondentId,
                ]);
            } elseif (empty($occurrence->seventh_respondent_id)) {
                ProgressionOccurrence::where('employee_id', $employeeId)
                ->update([
                    'seventh_respondent_id' => $respondentId,
                ]);
            }
        } else {
            // echo "insert occurrence <br>";
            //insert occurrence
            $occurrence = new ProgressionOccurrence;
            $occurrence->employee_id = $employeeId;
            $occurrence->first_respondent_id = $respondentId;
            $occurrence->save();
        }

        if ($occurrence->id) {
            // echo "get id occurrence->id==" . $occurrence->id . "<br>";
            $this->progressionOffenseOccurrence($employeeId, $categoryId, $occurrence->id);
        }
    }

    private function progressionOffenseOccurrence(int $employeeId, int $categoryId, int $occurrenceId)
    {
        // get id progressionOccurrence
        $offense = ProgressionOffense::where('employee_id', $employeeId)->first();
        // echo "count(offense) ==" . count($offense) . "<br>";

        if (count($offense) > 0 && $occurrenceId) {
            // echo "update offense" . "<br>";

            //update offense
            if (empty($offense->schedule_adherence_po_id) && $categoryId == 1) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'schedule_adherence_po_id' => $occurrenceId,
                    ]);
            } elseif (empty($offense->performance_related_po_id) && $categoryId == 2) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'performance_related_po_id' => $occurrenceId,
                    ]);
            } elseif (empty($offense->behaviour_related_po_id) && $categoryId == 3) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'behaviour_related_po_id' => $occurrenceId,
                    ]);
            } elseif (empty($offense->information_security_po_id) && $categoryId == 4) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'information_security_po_id' => $occurrenceId,
                    ]);
            } elseif (empty($offense->breach_trust_po_id) && $categoryId == 5) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'breach_trust_po_id' => $occurrenceId,
                    ]);
            } elseif (empty($offense->gross_negligence_po_id) && $categoryId == 6) {
                ProgressionOffense::where('employee_id', $employeeId)
                    ->update([
                        'gross_negligence_po_id' => $occurrenceId,
                    ]);
            }
        } else {
            // echo "insert offense" . "<br>";

            $progressionOffense = new ProgressionOffense;
            $progressionOffense->employee_id = $employeeId;

            if ($categoryId == 1) {
                $progressionOffense->schedule_adherence_po_id = $occurrenceId;
            } elseif ($categoryId == 2) {
                $progressionOffense->performance_related_po_id = $occurrenceId;
            } elseif ($categoryId == 3) {
                $progressionOffense->behaviour_related_po_id = $occurrenceId;
            } elseif ($categoryId == 4) {
                $progressionOffense->information_security_po_id = $occurrenceId;
            } elseif ($categoryId == 5) {
                $progressionOffense->breach_trust_po_id = $occurrenceId;
            } elseif ($categoryId == 6) {
                $progressionOffense->gross_negligence_po_id = $occurrenceId;
            }

            $progressionOffense->save();
        }
    }

    public static function progressionOccurenceSave($respondent, $date)
    {
        $progression_softdelete = ProgressionOccurrence::withTrashed()->where('first_respondent_id', $respondent->id)
        ->orWhere('second_respondent_id', $respondent->id)
        ->orWhere('third_respondent_id', $respondent->id)
        ->orWhere('fourth_respondent_id', $respondent->id)
        ->orWhere('fifth_respondent_id', $respondent->id)
        ->orWhere('sixth_respondent_id', $respondent->id)
        ->orWhere('seventh_respondent_id', $respondent->id)
        ->restore();

        if (!$progression_softdelete) {
            $close_ir = [
                'respondent_user_id' => $respondent->respondent_user_id,
                'offense_id' => $respondent->complainant->offense,
                'attendance_points_system_id' => $respondent->complainant->attendancepointssystem,
                'respondent_id' => $respondent->id
            ];
            // $progression = ProgressionOccurrence::where('employee_id', $request->respondent_user_id);

            // if ($respondent->complainant->attendancepointssystem) {
            //     $progression = $progression
            //         ->where(
            //             'attendance_points_system_id',
            //             $respondent->complainant->attendancepointssystem->id
            //         );
            // }
            if ($respondent->respondent_user_id && $respondent->complainant->offense->id) {
                $progression = ProgressionOccurrence::where('employee_id', $respondent->respondent_user_id)
                ->where('offense_id', $respondent->complainant->offense->id)
                ->get();
            }

            if (count($progression) == 0) {
                self::createProgressionOccurence($close_ir);
            } elseif (count($progression) > 0) {
                $loop_data = [
                    'second_respondent_id',
                    'third_respondent_id',
                    'fourth_respondent_id',
                    'fifth_respondent_id',
                    'sixth_respondent_id',
                    'seventh_respondent_id'
                ];
                foreach ($loop_data as $value) {
                    if ($progression[0]->$value == null) {
                        self::checkDateRange($progression[0]->updated_at, Carbon::today(), $date, $close_ir, $value);
                        break;
                    }
                }
            }
        }
    }

    public static function checkDateRange($progression_date, $date_today, $date, $close_ir, $update_collumn)
    {
        if (strtotime($progression_date->addDays($date)) >= strtotime($date_today) ||
            strtotime($progression_date) <= strtotime($date_today)) {
            self::updateProgressionOccurence($close_ir, $update_collumn);
        } else {
            self::createProgressionOccurence($close_ir);
        }
    }

    public static function createProgressionOccurence($close_ir)
    {
        $progression_occurence = new ProgressionOccurrence;
        $progression_occurence->employee_id = $close_ir['respondent_user_id'];
        if ($close_ir['offense_id'] != null) {
            $progression_occurence->offense_id = $close_ir['offense_id']->id;
        }
        if ($close_ir['attendance_points_system_id'] != null) {
            $progression_occurence->attendance_points_system_id = $close_ir['attendance_points_system_id']->id;
        }
        $progression_occurence->first_respondent_id = $close_ir['respondent_id'];
        $progression_occurence->save();

        return $progression_occurence;
    }

    public static function updateProgressionOccurence($close_ir, $update_collumn)
    {
        $update_data = ProgressionOccurrence::where('employee_id', $close_ir['respondent_user_id']);
        if ($close_ir['attendance_points_system_id'] != null) {
            $update_data = $update_data->where(
                'attendance_points_system_id',
                $close_ir['attendance_points_system_id']->id
            );
        }
        if ($close_ir['offense_id'] != null) {
            $update_data = $update_data->where('offense_id', $close_ir['offense_id']->id);
        }
        $update_data = $update_data->update([
            $update_collumn => $close_ir['respondent_id']
        ]);

        return $update_data;
    }

    public function refreshPeriod()
    {
        /* Progression 6 Months Cleansed*/
        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('first_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['first_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('second_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['second_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('third_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['third_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('fourth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['fourth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('fifth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['fifth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('sixth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['sixth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '6 %');
        })
        ->whereHas('seventh_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(6));
        })
        ->update(['seventh_occurrence' => 'cleansed']);

        /* Progression 12 Months Cleansed*/
        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('first_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['first_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('second_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['second_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('third_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['third_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('fourth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['fourth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('fifth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['fifth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('sixth_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['sixth_occurrence' => 'cleansed']);

        ProgressionOccurrence::whereHas('offense.gravity', function ($query) {
            $query->where('prescriptive_period', 'LIKE', '12 %');
        })
        ->whereHas('seventh_respondent.incident_report', function ($query) {
            $query->where('date_da', '<', Carbon::now()->subMonth(12));
        })
        ->update(['seventh_occurrence' => 'cleansed']);

        /* Refresher Period*/
        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->whereNull('second_respondent_id')
        ->orWhereNull('second_occurrence')
        ->update([
            'second_occurrence' => '1st Instance',
            'third_occurrence' => '2nd Instance',
            'fourth_occurrence' => '3rd Instance',
            'fifth_occurrence' => '4th Instance',
            'sixth_occurrence' => '5th Instance',
            'seventh_occurrence' => '6th Instance'
        ]);

        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->where('second_occurrence', 'cleansed')
        ->whereNull('third_respondent_id')
        ->orWhereNull('third_occurrence')
        ->update([
            'third_occurrence' => '1st Instance',
            'fourth_occurrence' => '2nd Instance',
            'fifth_occurrence' => '3rd Instance',
            'sixth_occurrence' => '4th Instance',
            'seventh_occurrence' => '5th Instance'
        ]);

        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->where('second_occurrence', 'cleansed')
        ->where('third_occurrence', 'cleansed')
        ->whereNull('fourth_respondent_id')
        ->orWhereNull('fourth_occurrence')
        ->update([
            'fourth_occurrence' => '1st Instance',
            'fifth_occurrence' => '2nd Instance',
            'sixth_occurrence' => '3rd Instance',
            'seventh_occurrence' => '4th Instance'
        ]);

        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->where('second_occurrence', 'cleansed')
        ->where('third_occurrence', 'cleansed')
        ->where('fourth_occurrence', 'cleansed')
        ->whereNull('fifth_respondent_id')
        ->orWhereNull('fifth_occurrence')
        ->update([
            'fifth_occurrence' => '1st Instance',
            'sixth_occurrence' => '2nd Instance',
            'seventh_occurrence' => '3rd Instance'
        ]);

        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->where('second_occurrence', 'cleansed')
        ->where('third_occurrence', 'cleansed')
        ->where('fourth_occurrence', 'cleansed')
        ->where('fifth_occurrence', 'cleansed')
        ->whereNull('sixth_respondent_id')
        ->orWhereNull('sixth_occurrence')
        ->update([
            'sixth_occurrence' => '1st Instance',
            'seventh_occurrence' => '2nd Instance'
        ]);

        ProgressionOccurrence::where('first_occurrence', 'cleansed')
        ->where('second_occurrence', 'cleansed')
        ->where('third_occurrence', 'cleansed')
        ->where('fourth_occurrence', 'cleansed')
        ->where('fifth_occurrence', 'cleansed')
        ->where('sixth_occurrence', 'cleansed')
        ->whereNull('seventh_respondent_id')
        ->orWhereNull('seventh_occurrence')
        ->update(['seventh_occurrence' => '1st Instance']);
    }
}
