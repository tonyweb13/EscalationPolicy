<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Settings\Coc\{Offense, Instance};
use App\Models\{ProgressionOccurrence, User};
use Illuminate\Http\JsonResponse;

class OffenseController extends Controller
{
    public function index(): JsonResponse
    {
        $offense = Offense::withTrashed()->with(['category', 'gravity',])->get();

        return response()->json($offense);
    }

    public function create(Request $request)
    {
        $offense = new Offense;
        $offense->offense = $request->offense;
        $offense->category_id = $request->category_id;
        $offense->gravity_id = $request->gravity_id;
        $offense->description = $request->description;
        $offense->save();

        if ($request->first_instance_coc_id || $request->second_instance_coc_id
            || $request->third_instance_coc_id || $request->fourth_instance_coc_id
            || $request->fifth_instance_coc_id || $request->sixth_instance_coc_id
            || $request->seventh_instance_coc_id) {
                $instance = new Instance;
                $instance->offense_id = $offense->id;
                $instance->first_instance_coc_id = $request->first_instance_coc_id;
                $instance->second_instance_coc_id = $request->second_instance_coc_id;
                $instance->third_instance_coc_id = $request->third_instance_coc_id;
                $instance->fourth_instance_coc_id = $request->fourth_instance_coc_id;
                $instance->fifth_instance_coc_id = $request->fifth_instance_coc_id;
                $instance->sixth_instance_coc_id = $request->sixth_instance_coc_id;
                $instance->seventh_instance_coc_id = $request->seventh_instance_coc_id;
                $instance->save();

                $offense_instance = Offense::where('id', $offense->id)->update([
                    'instance_id' => $instance->id
                ]);
        }

        return $offense;
    }

    public function show(int $id, Request $request)
    {
        $offense = Offense::withTrashed()->with(['category', 'gravity'])
        ->updateOrCreate(['id' => $id], $request->all());

        $instance = Instance::where('offense_id', $id)->updateOrCreate([
            'first_instance_coc_id' => $request->first_instance_coc_id,
            'second_instance_coc_id' => $request->second_instance_coc_id,
            'third_instance_coc_id' => $request->third_instance_coc_id,
            'fourth_instance_coc_id' => $request->fourth_instance_coc_id,
            'fifth_instance_coc_id' => $request->fifth_instance_coc_id,
            'sixth_instance_coc_id' => $request->sixth_instance_coc_id,
            'seventh_instance_coc_id' => $request->seventh_instance_coc_id,
        ]);

        if ($instance) {
            $offense_instance = Offense::where('id', $offense->id)->update([
                'instance_id' => $instance->id
            ]);
        }

        return $offense;
    }

    public function edit(int $id)
    {
        $offense = Offense::all()->where('id', $id)->first();

        return $offense;
    }

    public function destroy(int $id)
    {
        $offense = Offense::findOrFail($id)->delete();

        return response()->json($offense);
    }

    public function dropdown(int $catid): JsonResponse
    {
        $offense = Offense::select('id as value', 'offense as text')->with(['category', 'gravity'])
                    ->where('category_id', $catid)
                    ->get();

        return response()->json($offense);
    }

    public static function codeofconduct(int $id)
    {
        $offense = Offense::withTrashed()
        ->with([
            'category',
            'gravity',
            'instance.first_instance',
            'instance.second_instance',
            'instance.third_instance',
            'instance.fourth_instance',
            'instance.fifth_instance',
            'instance.sixth_instance',
            'instance.seventh_instance',
        ])
        ->findOrFail($id);
        if ($offense->deleted_at) {
            $offense['offense_archived'] = 'Offense Already Archived';
        }

        return $offense;
    }

    public static function codeofconductcoaching(int $id)
    {
        $offense = Offense::withTrashed()->with(['category', 'gravity'])->findOrFail($id);
        if ($offense->deleted_at) {
            $offense['offense_archived'] = 'Offense Already Archived';
        }

        return $offense;
    }

    public function progressionOccurenceShow($offense_id, $emp_no)
    {
        $progression = ProgressionOccurrence::with([
            'offense.category',
            'offense.gravity',
            'offense.instance.first_instance',
            'offense.instance.second_instance',
            'offense.instance.third_instance',
            'offense.instance.fourth_instance',
            'offense.instance.fifth_instance',
            'offense.instance.sixth_instance',
            'offense.instance.seventh_instance',
        ])
        ->where('offense_id', $offense_id)->where('employee_id', $emp_no)->first();
        if (!$progression) {
            $progression = 'No occurrence';
        }

        return $progression;
    }

    public static function codeofconductwithinstance(int $id, $emp_no = null)
    {
        $employee_ids = explode(',', $emp_no);
        $offense = Offense::withTrashed()->with(['category', 'gravity'])->findOrFail($id);
        if ($offense->deleted_at) {
            $offense['offense_archived'] = 'Offense Already Archived';
        }
        $offense_instance_array = [];
        foreach ($employee_ids as  $value) {
            if ($value != 0) {
                $user = User::find($value);
                $offense_instance = ProgressionOccurrence::where('employee_id', (int)$value)
                    ->where('offense_id', $id)
                    ->first();

                    if ($offense_instance->first_occurrence != null &&
                    $offense_instance->first_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->first_occurrence);
                    } elseif ($offense_instance->first_occurrence == 'cleansed' &&
                    $offense_instance->second_occurrence != null &&
                    $offense_instance->second_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->second_occurrence);
                    } elseif ($offense_instance->second_occurrence == 'cleansed' &&
                    $offense_instance->third_occurrence != null &&
                    $offense_instance->third_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->third_occurrence);
                    } elseif ($offense_instance->third_occurrence == 'cleansed' &&
                    $offense_instance->fourth_occurrence != null &&
                    $offense_instance->fourth_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->fourth_occurrence);
                    } elseif ($offense_instance->fourth_occurrence == 'cleansed' &&
                    $offense_instance->fifth_occurrence != null &&
                    $offense_instance->fifth_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->fifth_occurrence);
                    } elseif ($offense_instance->fifth_occurrence == 'cleansed' &&
                    $offense_instance->sixth_occurrence != null &&
                    $offense_instance->sixth_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->sixth_occurrence);
                    } elseif ($offense_instance->sixth_occurrence == 'cleansed' &&
                    $offense_instance->seventh_occurrence != null &&
                    $offense_instance->seventh_occurrence != 'cleansed') {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - ' . $offense_instance->seventh_occurrence);
                    } elseif ($offense_instance->seventh_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 7th Instance)');
                    } elseif ($offense_instance->sixth_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 6th Instance)');
                    } elseif ($offense_instance->fifth_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 5th Instance)');
                    } elseif ($offense_instance->fourth_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 4th Instance)');
                    } elseif ($offense_instance->third_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 3rd Instance)');
                    } elseif ($offense_instance->second_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 2nd Instance)');
                    } elseif ($offense_instance->first_occurrence == "cleansed") {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance' . ' (refresh in 1st Instance)');
                    } elseif ($offense_instance->seventh_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 7th Instance');
                    } elseif ($offense_instance->sixth_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 7th Instance');
                    } elseif ($offense_instance->fifth_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 6th Instance');
                    } elseif ($offense_instance->fourth_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 5th Instance');
                    } elseif ($offense_instance->third_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 4th Instance');
                    } elseif ($offense_instance->second_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 3rd Instance');
                    } elseif ($offense_instance->first_respondent_id) {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 2nd Instance');
                    } else {
                        array_push($offense_instance_array, $user->first_name . ' ' .
                        $user->last_name . ' - 1st Instance');
                    }
            }
        }
        $offense['instance'] = $offense_instance_array;

        return $offense;
    }

    public function caseClosure($id)
    {
        $offense = Offense::with([
            'instance.first_instance',
            'instance.second_instance',
            'instance.third_instance',
            'instance.fourth_instance',
            'instance.fifth_instance',
            'instance.sixth_instance',
            'instance.seventh_instance',
        ])
        ->where('id', $id)->first();

        return response()->json($offense);
    }

    public function category(int $catid): JsonResponse
    {
        $offense = Offense::with([
                        'category',
                        'gravity',
                        'instance.first_instance',
                        'instance.second_instance',
                        'instance.third_instance',
                        'instance.fourth_instance',
                        'instance.fifth_instance',
                        'instance.sixth_instance',
                        'instance.seventh_instance',
                    ])
                    ->where('category_id', $catid)->get();

        return response()->json($offense);
    }

    public function dropdownMultiple(int $catid, $offenses): JsonResponse
    {
        $offese_list = explode(",", $offenses);
        $offense = Offense::select('id as value', 'offense as text')->with(['category', 'gravity', 'instance'])
                    ->where('category_id', $catid)
                    ->whereNotIn('id', $offese_list)
                    ->get();


        return response()->json($offense);
    }

    public static function codeofconductmultiple(int $id, $offenses, $emp_no)
    {
        $employee_ids = explode(',', $emp_no);
        $offense = Offense::with(['category', 'gravity'])
        ->whereNotIn('id', [$offenses])
        ->findOrFail($id);

        $offense_instance_array = [];
        foreach ($employee_ids as  $value) {
            if ($value != 0) {
                $user = User::find($value);
                $offense_instance = ProgressionOccurrence::where('employee_id', (int)$value)
                    ->where('offense_id', $id)
                    ->first();

                // if ($offense_instance->seventh_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 7th Instance');
                // } elseif ($offense_instance->sixth_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 6th Instance');
                // } elseif ($offense_instance->fifth_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 5th Instance');
                // } elseif ($offense_instance->fourth_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 4th Instance');
                // } elseif ($offense_instance->third_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 3rd Instance');
                // } elseif ($offense_instance->second_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 2nd Instance');
                // } elseif ($offense_instance->first_respondent_id) {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 1st Instance');
                // } else {
                //     array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 1st Instance');
                // }

                if ($offense_instance->seventh_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 7th Instance');
                } elseif ($offense_instance->sixth_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 7th Instance');
                } elseif ($offense_instance->fifth_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 6th Instance');
                } elseif ($offense_instance->fourth_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 5th Instance');
                } elseif ($offense_instance->third_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 4th Instance');
                } elseif ($offense_instance->second_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 3rd Instance');
                } elseif ($offense_instance->first_respondent_id) {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 2nd Instance');
                } else {
                    array_push($offense_instance_array, $user->first_name . ' ' . $user->last_name . ' - 1st Instance');
                }
            }
        }
        $offense['instance'] = $offense_instance_array;

        return $offense;
    }

    public function searchQuery($searchKey)
    {
       $offense = Offense::with([
            'category',
            'gravity',
            'instance.first_instance',
            'instance.second_instance',
            'instance.third_instance',
            'instance.fourth_instance',
            'instance.fifth_instance',
            'instance.sixth_instance',
            'instance.seventh_instance',
        ])
       ->where('offense', 'LIKE' , '%'.$searchKey.'%')
       ->orWhereHas('category', function ($query) use ($searchKey) {
            $query->where('name', 'LIKE' , '%'.$searchKey.'%');
        })
        ->orWhereHas('gravity', function ($query) use ($searchKey) {
            $query->where('gravity', 'LIKE' , '%'.$searchKey.'%');
        })
       ->orderBy('category_id')
       ->get();

        return response()->json($offense);
    }
}
