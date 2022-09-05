<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Respondent;

class EmployeeController extends Controller
{
    public function statusComplainantRespondent(int $statusid) : JsonResponse
    {
        $complainant = Respondent::with([
            'respondent_user',
            'complainant',
            'complainant.complainant_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.attachments',
            'complainant.offense.gravity',
            'complainant.witness_user.witness_fullname',
            'complainant.attachments',
            'incident_report',
            'respondent_hearing',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'respondent_hearing',
        ])
            ->where('status_id', $statusid)
            ->whereHas('complainant', function ($query) {
                $query->where('complainant_user_id', Auth()->id());
                $query->orWhere('respondent_user_id', Auth()->id());
            })
            // ->orWhere('respondent_user_id', Auth()->id())
            ->where('status_id', '!=', 4)
            ->orderBy('ir_number', 'desc')
            ->get();

        return response()->json($complainant);
    }

    public function newInprogress() : JsonResponse
    {
        $complainant = Respondent::with([
            'hrbp_user',
            'respondent_user',
            'complainant',
            'complainant.complainant_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attendancepointssystem.attendancepoint',
            'complainant.witness_user.witness_fullname',
            'complainant.attachments',
            'incident_report',
            'respondent_hearing',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'incident_report.hr_attachments',
            'supervisor_approval'
        ])
            ->whereHas('complainant', function ($query) {
                $query->where('complainant_user_id', Auth()->id());
            })
            ->where(function ($query) {
                $query->where('status_id', 1);
                $query->orWhere('status_id', 2);
            })
            ->orWhere('respondent_user_id', Auth()->id())
            ->where('status_id', 2)
            ->orderBy('ir_number', 'desc')
            ->get();

        return response()->json($complainant);
    }

    public function onhold() : JsonResponse
    {
        $complainant = Respondent::with([
            'hrbp_user',
            'respondent_user',
            'complainant',
            'complainant.complainant_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.attachments',
            'complainant.offense.gravity',
            'complainant.witness_user.witness_fullname',
            'complainant.attachments',
            'incident_report',
            'respondent_hearing',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'incident_report.hr_attachments'
        ])
            ->where('status_id', 3)
            ->whereHas('complainant', function ($query) {
                $query->where('complainant_user_id', Auth()->id());
                $query->orWhere('respondent_user_id', Auth()->id());
            })
            ->orderBy('ir_number', 'desc')
            ->get();

        return response()->json($complainant);
    }

    public function invite() : JsonResponse
    {
        $invite = Respondent::with([
            'complainant',
            'complainant.complainant_user',
            'respondent_user',
            'complainant.witness_user.witness_fullname',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'incident_report.invite_user.invite_fullname',
            'incident_report.invite_ir',
        ])
            ->where('status_id', '!=', 1)
            ->where('status_id', '!=', 4)
            ->whereHas('incident_report.invite_user', function ($query) {
                $query->where('invite_user_id', Auth()->id());
                $query->whereNotNull('date_admin_hearing');
            })
            ->orderBy('ir_number', 'desc')
            ->get();

        return response()->json($invite);
    }
}
