<?php

namespace App\Http\Controllers\Api\Admin\Settings\Coc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Settings\Coc\Category;
use App\Models\Admin\Settings\Coc\{Offense, AttendancePointsSystem};
use Excel;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{

    public function index(): JsonResponse
    {
        $category = Category::all();
        return response()->json($category);
    }

    public function create(Request $request)
    {
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return $category;
    }

    public function show(int $id, Request $request)
    {
        $category = Category::updateOrCreate(['id' => $id], $request->all());

        return $category;
    }

    public function edit(int $id)
    {
        $category = Category::all()->where('id', $id)->first();

        return $category;
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id)->delete();

        return response()->json($category);
    }

    public function dropdown(): JsonResponse
    {
        $category = Category::select('id as value', 'name as text')
        ->where('name', 'NOT LIKE', '%Point%') /*TODO: in future Attendance Points will hidden  */
        ->get();

        return response()->json($category);
    }

    public function downloadExcel()
    {
        $form = Excel::create('Code Of Conduct', function ($excel) {
            $category = [
                'Attendance',
                'CWD Policy',
                'Negligence',
                'General House Rules',
                'Fraud',
                'Misconduct',
                'Poor Work Ethics',
                'Information Security',
                'Zero Tolerance Policy',
                'Compliance Breach',
                'General Company Rules',
                'Negligence PIP',
                'LMS Guidelines',
                'Corrective Policy',
                'Hard Return',
                'Warranted Escalation',
                'Attendance Supervisor & Manager',
                'Attendance Trainer',
                'Credit Repair Offer',
                'Credit Repair Offer Supervisor',
            ];
            foreach ($category as $key => $value) {
                $key = $key + 1;
                $offense_data = [];

                /* Ignore Attendance With Points */
                if ($key >= 15) {
                    $keyChange = $key+1;
                    $offenses_list = Offense::where('category_id', $keyChange)->with([
                                    'gravity',
                                    'instance.first_instance',
                                    'instance.second_instance',
                                    'instance.third_instance',
                                    'instance.fourth_instance',
                                    'instance.fifth_instance',
                                    'instance.sixth_instance',
                                    'instance.seventh_instance',
                                ])->get();
                } else {
                    $offenses_list = Offense::where('category_id', $key)->with([
                                    'gravity',
                                    'instance.first_instance',
                                    'instance.second_instance',
                                    'instance.third_instance',
                                    'instance.fourth_instance',
                                    'instance.fifth_instance',
                                    'instance.sixth_instance',
                                    'instance.seventh_instance',
                                ])->get();
                }

                foreach ($offenses_list as $val) {
                    $push_data = [
                        'Offense' => $val->offense,
                        'Gravity' => $val->gravity->gravity,
                        'Prescriptive Period' => $val->gravity->prescriptive_period,
                        'Description' => strip_tags($val->description),
                        'First Instance' => $val->instance->first_instance->name,
                        'Second Instance' => $val->instance->second_instance->name,
                        'Third Instance' => $val->instance->third_instance->name,
                        'Fourth Instance' => $val->instance->fourth_instance->name,
                        'Fifth Instance' => $val->instance->fifth_instance->name,
                        'Sixth Instance' => $val->instance->sixth_instance->name,
                        'Seventh Instance' => $val->instance->seventh_instance->name
                    ];
                    array_push($offense_data, $push_data);
                }
                $excel->sheet($value, function ($sheet) use ($offense_data) {
                    $sheet->fromArray($offense_data);
                });
            }
        })->download('xlsx');

        return response()->download($form);
    }
}
