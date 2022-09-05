<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use App;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ValidatorController extends Controller
{
    public static function hrbpClusterValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'hrsl_employee_no' => 'required|integer',
            'hrsl_email_address' => 'required|email',
            'hrsl_location' => [
                'required',
                'string',
                Rule::in(['Net Square','Two/Neo','Alabang','Bacolod','Market Market']),
            ],
            'hrbp_employee_no' => 'required|integer',
            'hrbp_email_address' => 'required|email',
            'user_employee_no' => 'required|integer',
            'supervisor_employee_no' => 'required|integer',
            'manager_employee_no' => 'required|integer',
        ]);
        return $validator;
    }

    public static function disputeDashboardValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'employee_id' => 'required|integer',
            'employee_name' => 'required|string|regex:/^[a-zA-Z,.!? ]*$/',
            'designation' => 'required|string|regex:/^[a-zA-Z,.!? ]*$/',
            'priority' => 'required|string|regex:/^[a-zA-Z0-9,.!? ]*$/',
            'tenureship' => 'required|string|regex:/^[a-zA-Z,.!? ]*$/',
            'portfolio' => 'required|string|regex:/^[a-zA-Z,.!? ]*$/',
            'site_location' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9,.!? ]*$/',
                Rule::in(['Net Square','Two/Neo','Alabang','Bacolod','Market Market']),
            ],
            'days' => 'required|string|regex:/^[a-zA-Z,.!-? ]*$/',
            'coverage' => 'required|string|regex:/^[a-zA-Z,.!-? ]*$/i',
            'scg' => 'required|integer',
            'mtdg' => 'required|integer',
            'team' => 'required|string',
            'cluster' => 'required|string',
            'loan_amount' => 'required|numeric',
            'attendance' => 'required|regex:/^[a-zA-Z,.!-? ]*$/i',
            'in_call' => 'required|numeric',
            'ready' => 'required|numeric',
            'wrap_up' => 'required|numeric',
            'not_ready' => 'required|numeric',
            'coveragetotal_loan_amount' => 'required|numeric',
            'mtdtotal_loan_amount' => 'required|numeric',
            'attendancereliability' => 'required|numeric',
            'major' => 'required|integer',
            'minor' => 'required|integer',
            'major' => 'required|integer',
            'reportdate' => 'required|date',
            'week_count' => 'required|string|regex:/^[a-zA-Z0-9,.!? ]*$/',
            'running_week' => 'required|regex:/^[a-zA-Z,.!-? ]*$/i',
        ]);

        return $validator;
    }

    public static function IrrValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'name' => 'required|string',
        ]);
        return $validator;
    }

    public static function IncidentReportEditFormValidator($value)
    {
        if (sizeof($value->irr_multiple) > 1 && sizeof($value->instance_multiple) > 1) {
            $getRules = [
                'irr_id' => 'required',
                'instance_multiple' => 'required',
                'irr_multiple' => 'required',
            ];

            $getMessages = [
                'irr_id.required' => ' Generate Case Closure is Required',
                'instance_multiple.required' => ' Final Instance is Required',
                'irr_multiple.required' => ' Final Case Closure is Required',
            ];
        } else {
            $getRules = [
                'irr_id' => 'required',
                'final_instance' => 'required',
                'final_irr_single' => 'required',
            ];

            $getMessages = [
                'irr_id.required' => ' Generate Case Closure is Required',
                'final_instance.required' => ' Final Instance is Required',
                'final_irr_single.required' => ' Final Case Closure is Required',
            ];
        }

        $validator = Validator::make($value->toArray(), $getRules, $getMessages);

        return $validator;
    }

    public static function AcknowledgementReceiptCreateValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'user_id' => 'required',
            'incentive' => 'required',
            'item' => 'required|string',
            'date_received' => 'required',
        ]);

        return $validator;
    }

    public static function AcknowledgementReceiptEmailValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'email_address' => 'required|email',
        ]);

        return $validator;
    }

    public static function VpsSiteLocationValidator($value)
    {
        $validator = Validator::make($value->toArray(), [
            'employee_no' => 'required|integer',
            'vps_office_location' => [
                'required',
                'string',
                Rule::in(['Two Neo','Alabang','Bacolod','Market Market']),
            ],
        ]);
        return $validator;
    }
}
