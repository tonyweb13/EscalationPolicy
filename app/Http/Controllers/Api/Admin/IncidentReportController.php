<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;
// use DateTime;
use DateInterval;
use DatePeriod;
use Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\Settings\Office;
use App\Models\Admin\Settings\Coc\{
        Category,
        Gravity,
        IncidentReportResolution,
        Offense
};
use App\Models\{
    Admin\IncidentReport,
    Admin\Article297Terminable,
    RespondentNotResponse,
    ProgressionOccurrence,
    Designation,
    OfficeLocation,
    Respondent,
    Witness,
    Invite,
    User,
    Attachment,
    Complainant,
    ComplainantEdit,
    ComplainantEditRequest,
    OnHoldRequest,
    HrbpCluster,
    Signatory,
    SupervisorApproval,
    UsersGroup,
    ReopenRequest
};
use App\Jobs\{
    EmailInvitation,
    EmailGeneratedNte,
    EmailGeneratedIrr,
    EmailModifyIncidentReport,
    EmailClosedIncidentReport,
    EmailManagerAcknowledgement,
    EmailNoReplyRespondent,
    EmailNoReplyOthers,
    EmailReplyRespondent,
    EmailSignatoryTransfer,
};
use App\Http\Controllers\Api\{
    Admin\ProgressionOffenseController,
    Admin\ClosedStatusController,
    Admin\Settings\Coc\OffenseController,
    HearingAttendanceController,
    ValidatorController,
};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Events\OpenClosedCasesCreated;
use Exception;
use Auth;


class IncidentReportController extends Controller
{
    public function create(Request $request)
    {
        $irr_int_array = [];
        $instance_int_array = [];

        $respo = User::select('first_name', 'last_name')->findOrFail($request->respondent_user_id);
        $respo_name = $respo['first_name']." ".$respo['last_name'];
        $incident = new IncidentReport;
        $incident->hr_user_id = $request->hr_user_id;
        $incident->complainant_id = $request->complainant_id;
        $incident->remarks = $request->remarks;
        $incident->is_generate_nte_invalid_ir = $request->is_generate_nte_invalid_ir;
        $incident->date_nte_serve = Carbon::now();
        $incident->preventive_suspension_start = $request->preventive_suspension_start;
        $incident->preventive_suspension_end = $request->preventive_suspension_end;
        $incident->date_admin_hearing = $request->date_admin_hearing;
        $incident->is_under_investigation = $request->is_under_investigation;
        $incident->type_invite = $request->type_invite;
        $incident->meeting_place = $request->meeting_place;
        $incident->time_admin_hearing = $request->time_admin_hearing;
        $incident->rtw = $request->rtw;
        $incident->rtw_date = $request->rtw_date;
        $incident->rtw_advice_date = $request->rtw_advice_date;
        $incident->sn_termination_date = $request->sn_termination_date;
        $incident->sn_date_absence_start = $request->sn_date_absence_start;
        $incident->sn_date_absence_end = $request->sn_date_absence_end;
        // $incident->ageing = 1; //move to complainant controller when submit a form

        if ($request->initial_irr_id) {
            $incident->initial_irr_id = $request->initial_irr_id;
        } else {
            foreach ($request->irr_multiple as $value) {
                array_push($irr_int_array, (int)$value);
            }
            $incident->initial_irr_id = json_encode($irr_int_array);
        }

        // if ($request->initial_instance) {
        //     $incident->initial_instance = $request->initial_instance;
        // } else {

        //     $incident->initial_instance = json_encode($request->instance_multiple);
        // }

        if (count($request->offense_id) > 1) {
            foreach ($request->instance_multiple as $val) {
                array_push($instance_int_array, $val);
            }
            $incident->initial_instance = json_encode($instance_int_array);
        } else {
            $incident->initial_instance = $request->initial_instance;
        }

        $incident->save();

        /* update ir_id and status_id after save IR */
        Respondent::where('id', $request->respondent_id)
        ->update(
            [
                'status_id' => $request->status_id,
                'ir_id' => $incident->id,
            ]
        );

        /* update when HR assign */
        if ($request->hr_user_id) {
            $respondent = User::findOrFail($request->hr_user_id);
            Respondent::where('id', $request->respondent_id)
            ->update(['hrbp_employee_no' => $respondent->employee_no]);
        }

        if ($request->status_id && $incident->id) {

            /* Start Signatories */
            $signatories = new Signatory();
            $signatories->ir_id = $incident->id;
            $signatories->prepared_by_first = $request->prepared_by_first;
            $signatories->prepared_by_last = $request->prepared_by_last;
            $signatories->prepared_by_designation = $request->prepared_by_designation;
            $signatories->prepared_by_empno = $request->prepared_by_empno;
            $signatories->requested_by_first = $request->requested_by_first;
            $signatories->requested_by_last = $request->requested_by_last;
            $signatories->requested_by_designation = $request->requested_by_designation;
            $signatories->requested_by_empno = $request->requested_by_empno;
            $signatories->approved_by_first = $request->approved_by_first;
            $signatories->approved_by_last = $request->approved_by_last;
            $signatories->approved_by_designation = $request->approved_by_designation;
            $signatories->approved_by_empno = $request->approved_by_empno;
            $signatories->noted1_by_first = $request->noted1_by_first;
            $signatories->noted1_by_last = $request->noted1_by_last;
            $signatories->noted1_by_designation = $request->noted1_by_designation;
            $signatories->noted1_by_empno = $request->noted1_by_empno;
            $signatories->noted2_by_first = $request->noted2_by_first;
            $signatories->noted2_by_last = $request->noted2_by_last;
            $signatories->noted2_by_designation = $request->noted2_by_designation;
            $signatories->noted2_by_empno = $request->noted2_by_empno;
            $signatories->noted3_by_first = $request->noted3_by_first;
            $signatories->noted3_by_last = $request->noted3_by_last;
            $signatories->noted3_by_designation = $request->noted3_by_designation;
            $signatories->noted3_by_empno = $request->noted3_by_empno;
            $signatories->save();
            /* End Signatories */

            /* if Status Closed, sent email */
            if ($request->status_id == 4) {
                EmailClosedIncidentReport::dispatch(
                    $request->ir_number,
                    $request->hr_user_id,
                    $request->complainant_user_id,
                    $request->respondent_user_id
                );
            }

            if ($request->witness_user_id) {
                foreach ($request->witness_user_id as $witness_user) {
                    $witness = new Witness;
                    $witness->ir_id = $incident->id;
                    $witness->complainant_id = $request->complainant_id;
                    $witness->hr_user_id = $request->hr_user_id;
                    $witness->witness_user_id = $witness_user;
                    $witness->save();
                }
            }

            if ($request->invite_user_id) {

                foreach ($request->invite_user_id as $invite_user) {
                    $invite = new Invite;
                    $invite->complainant_id = $request->complainant_id;
                    $invite->hr_user_id = $request->hr_user_id;
                    $invite->invite_user_id = $invite_user;
                    $invite->save();
                }

                if ($request->invite_user_id && $request->ir_number &&
                    $request->date_admin_hearing && $request->time_admin_hearing) {
                    EmailInvitation::dispatch(
                        $request->ir_number,
                        $request->complainant_user_id,
                        $request->respondent_user_id,
                        $request->date_admin_hearing,
                        $request->time_admin_hearing,
                        $request->invite_user_id
                    );
                }
            }

            if ($request->respondent_email) {
                $manager_email = '';
                if ($request->manager_email) {
                    $manager_email = $request->manager_email;
                }

                $respo = User::select('first_name', 'last_name')->findOrFail($request->respondent_user_id);
                $respo_name = $respo['first_name']." ".$respo['last_name'];

                EmailGeneratedNte::dispatch(
                    $request->ir_number,
                    $request->respondent_email,
                    $request->supervisor_email,
                    $manager_email,
                    $request->reported,
                    $respo_name
                );
            }
        }

        if (json_encode($request->attachments)) {
            $attach = new Attachment;
            $attach->complainant_id = $request->complainant_id;
            $attach->respondent_id = $request->respondent_id;
            $attach->attachments = json_encode($request->attachments);

            if ($attach->save()) {
                IncidentReport::where('id', $incident->id)
                    ->where('complainant_id', $request->complainant_id)
                    ->update([
                        'hr_attachments_id' => $attach->id,
                    ]);
            }
        }

        return $incident;
    }

    public function updateIncident(int $id, Request $request)
    {
        $incident = IncidentReport::updateOrCreate(
            ['id' => $id],
            [
                'remarks' => $request->remarks,
                'is_generate_nte_invalid_ir' => $request->is_generate_nte_invalid_ir,
                'preventive_suspension_start' => $request->preventive_suspension_start,
                'preventive_suspension_end' => $request->preventive_suspension_end,
                'date_admin_hearing' => $request->date_admin_hearing,
                'time_admin_hearing' => $request->time_admin_hearing,
                'is_under_investigation' => $request->is_under_investigation,
                'type_invite' => $request->type_invite,
                'hr_user_id' => $request->hr_user_id,
                'meeting_place' => $request->meeting_place,
                'rtw' => $request->rtw,
                'agreement' => $request->agreement,
                'rtw_date' => $request->rtw_date,
                'rtw_advice_date' => $request->rtw_advice_date,
                'sn_termination_date' => $request->sn_termination_date,
                'sn_date_absence_start' => $request->sn_date_absence_start,
                'sn_date_absence_end' => $request->sn_date_absence_end,
                'additional_notes' => $request->additional_notes,
                'is_visible_respondent' => 0,
            ]
        );

        /* update when HR assign */
        if ($request->hr_user_id) {
            $respondent = User::findOrFail($request->hr_user_id);
            Respondent::where('id', $request->respondent_id)
            ->update(['hrbp_employee_no' => $respondent->employee_no]);
        }

        if ($request->nte_stage_case && $request->respondent_id) {
            Respondent::where('id', $request->respondent_id)
            ->update(['nte_status_stage_case' => $request->nte_stage_case]);
        }

        if ($request->prepared_by_empno || $request->requested_by_empno) {

            $signatures = Signatory::where('ir_id', $id)->first();

            /* Email Prepared by */
            if ($signatures->prepared_by_first != $request->prepared_by_first
            || $signatures->prepared_by_last != $request->prepared_by_last
            || $signatures->prepared_by_empno != $request->prepared_by_empno
            || $signatures->prepared_by_designation != $request->prepared_by_designation) {

            $user = User::where('employee_no', $signatures->prepared_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->prepared_by_designation
                    );
                }
            }

            /* Email Requested by */
            if ($signatures->requested_by_first != $request->requested_by_first
            || $signatures->requested_by_last != $request->requested_by_last
            || $signatures->requested_by_empno != $request->requested_by_empno
            || $signatures->requested_by_designation != $request->requested_by_designation) {

            $user = User::where('employee_no', $signatures->requested_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->requested_by_designation
                    );
                }
            }

            /* Email Approved by */
            if ($signatures->approved_by_first != $request->approved_by_first
            || $signatures->approved_by_last != $request->approved_by_last
            || $signatures->approved_by_empno != $request->approved_by_empno
            || $signatures->approved_by_designation != $request->approved_by_designation) {

            $user = User::where('employee_no', $signatures->approved_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->approved_by_designation
                    );
                }
            }

            /* Email Noted1 by */
            if ($signatures->noted1_by_first != $request->noted1_by_first
            || $signatures->noted1_by_last != $request->noted1_by_last
            || $signatures->noted1_by_empno != $request->noted1_by_empno
            || $signatures->noted1_by_designation != $request->noted1_by_designation) {

            $user = User::where('employee_no', $signatures->noted1_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->noted1_by_designation
                    );
                }
            }

            /* Email Noted2 by */
            if ($signatures->noted2_by_first != $request->noted2_by_first
            || $signatures->noted2_by_last != $request->noted2_by_last
            || $signatures->noted2_by_empno != $request->noted2_by_empno
            || $signatures->noted2_by_designation != $request->noted2_by_designation) {

            $user = User::where('employee_no', $signatures->noted2_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->noted2_by_designation
                    );
                }
            }

            /* Email Noted2 by */
            if ($signatures->noted3_by_first != $request->noted3_by_first
            || $signatures->noted3_by_last != $request->noted3_by_last
            || $signatures->noted3_by_empno != $request->noted3_by_empno
            || $signatures->noted3_by_designation != $request->noted3_by_designation) {

            $user = User::where('employee_no', $signatures->noted3_by_empno)->first();

                if ($user->email_address) {
                    EmailSignatoryTransfer::dispatch(
                        $request->ir_number,
                        $user->email_address,
                        $signatures->noted3_by_designation
                    );
                }
            }

            Signatory::where('ir_id', $id)
            ->update([
                'prepared_by_first' => $request->prepared_by_first,
                'prepared_by_last' => $request->prepared_by_last,
                'prepared_by_empno' => $request->prepared_by_empno,
                'prepared_by_designation' => $request->prepared_by_designation
            ]);

            Signatory::where('ir_id', $id)
            ->update([
                'requested_by_first' => $request->requested_by_first,
                'requested_by_last' => $request->requested_by_last,
                'requested_by_empno' => $request->requested_by_empno,
                'requested_by_designation' => $request->requested_by_designation
            ]);

            Signatory::where('ir_id', $id)
            ->update([
                'approved_by_first' => $request->approved_by_first,
                'approved_by_last' => $request->approved_by_last,
                'approved_by_empno' => $request->approved_by_empno,
                'approved_by_designation' => $request->approved_by_designation
            ]);


            Signatory::where('ir_id', $id)
            ->update([
                'noted1_by_first' => $request->noted1_by_first,
                'noted1_by_last' => $request->noted1_by_last,
                'noted1_by_empno' => $request->noted1_by_empno,
                'noted1_by_designation' => $request->noted1_by_designation
            ]);

            Signatory::where('ir_id', $id)
            ->update([
                'noted2_by_first' => $request->noted2_by_first,
                'noted2_by_last' => $request->noted2_by_last,
                'noted2_by_empno' => $request->noted2_by_empno,
                'noted2_by_designation' => $request->noted2_by_designation
            ]);

            Signatory::where('ir_id', $id)
            ->update([
                'noted3_by_first' => $request->noted3_by_first,
                'noted3_by_last' => $request->noted3_by_last,
                'noted3_by_empno' => $request->noted3_by_empno,
                'noted3_by_designation' => $request->noted3_by_designation
            ]);
        }

            /* Start of in_lieu of ToE */
             $initial_irr = json_decode($incident->initial_irr_id);

             if (count($initial_irr) > 1) {
                 for ($i=0; $i < count($initial_irr); $i++) {
                     if ($initial_irr[$i] == 10) {
                         $in_lieu = true;
                     }
                 }
             } else {
                 if ($initial_irr == 10) {
                     $in_lieu = true;
                 }
             }

             if ($in_lieu && $request->mom) {
                 Respondent::where('id', $request->respondent_id)
                     ->update(
                         [
                             'mom' => $request->mom,
                         ]
                     );
             }
            /* End of in_lieu of ToE */

            /* $request->is_under_investigation = 1 (Generate Case Closure)*/
             if ($request->status && $id && $request->is_under_investigation == 1
                && ($request->final_irr_single || $request->irr_multiple)) {

                $validator = ValidatorController::IncidentReportEditFormValidator($request);

                if (!$validator->fails()) {

                    $get_suspension_date = '';
                    if (count($request->suspension_multiple) > 0 ) {

                        $sus_multi = [Carbon::parse($request->suspension_date)->format("Y-m-d")];
                        foreach ($request->suspension_multiple as $suspension_multi) {
                            foreach (json_decode($suspension_multi) as $suspension_date_multi) {
                                $sus_multi[] = Carbon::parse($suspension_date_multi)->format("Y-m-d");
                            }
                        }
                        array_push($sus_multi);
                        $get_suspension_date = json_encode($sus_multi);

                    } elseif($request->suspension_date) {
                        $get_suspension_date = Carbon::parse($request->suspension_date)->format("Y-m-d");
                    }

                    /* update Final IRR Single and IRR_ID*/
                    if ($request->final_irr_single) {

                            IncidentReport::where('id', $id)
                            ->update([
                                'irr_id' => $request->irr_id,
                                'final_instance' => $request->final_instance,
                                'final_irr_single' => $request->final_irr_single,
                                'date_da' => Carbon::now()->format('Y-m-d'),
                                'suspension_date' => $get_suspension_date
                            ]);

                    } else {

                        /* update Final IRR MULTIPLE and IRR_ID*/
                        $instance_int_array = [];
                        $irr_int_array = [];
                        if (sizeof($request->irr_multiple) > 1 && sizeof($request->instance_multiple) > 1) {
                            foreach ($request->instance_multiple as $instance_multi) {
                                array_push($instance_int_array, $instance_multi);
                            }
                            foreach ($request->irr_multiple as $value) {
                                array_push($irr_int_array, (int)$value);
                            }
                            IncidentReport::where('id', $id)
                                ->update([
                                    'irr_id' => $request->irr_id,
                                    'final_instance' => json_encode($instance_int_array),
                                    'final_irr_multiple' => json_encode($irr_int_array),
                                    'date_da' => Carbon::now()->format('Y-m-d'),
                                    'suspension_date' => $get_suspension_date
                                ]);
                        }
                    }

                    /* if irr_id not empty automatic status will be Close */
                    Respondent::where('id', $request->respondent_id)
                    ->update(['status_id' => 4]);

                    /* Start Email Manager Acknowledge */
                    if (Str::contains($request->requested_by_designation, 'Manager') == 1) {
                        $manager = User::where('employee_no', $request->requested_by_empno)->first();
                        $manager_email = $manager->email_address;
                        $manager_name = $request->requested_by_first . " " . $request->requested_by_last;

                    } elseif (Str::contains($request->approved_by_designation, 'Manager') == 1) {
                        $manager = User::where('employee_no', $request->approved_by_empno)->first();
                        $manager_email = $manager->email_address;
                        $manager_name = $request->approved_by_first . " " . $request->approved_by_last;
                    }

                    if ($request->respondent_user_id) {
                        $employee = User::where('id', $request->respondent_user_id)->first();
                        $employee_name = $employee->first_name . " " . $employee->last_name;
                    }

                    if ($id && $request->respondent_user_id && $manager_email && $employee_name && $get_suspension_date) {

                        if (count($request->suspension_multiple) > 0) {

                            $rep_suspension_date = str_replace(array('[', ']', '"'), '', $get_suspension_date);
                            $exp_suspension_date = explode(",", $rep_suspension_date);

                            foreach($exp_suspension_date as &$suspension_dates)
                            {
                                $suspension_dates = date("F d, Y", strtotime($suspension_dates));
                            }
                            $email_suspension_date = implode(', ', $exp_suspension_date);

                        } else {
                            $email_suspension_date = Carbon::parse($request->suspension_date)->format("F d, Y");
                        }

                        EmailManagerAcknowledgement::dispatch(
                            $id,
                            $request->ir_number,
                            $manager_name,
                            $employee_name,
                            $manager_email,
                            $email_suspension_date
                        );
                        IncidentReport::where('id', $id)->update([
                            'manager_acknowledge' => Carbon::now()->format("Y-m-d")
                        ]);
                    }
                    /* End Email Manager Acknowledge */

                    $reopenExists = ReopenRequest::where('respondent_id', $request->respondent_id)
                    ->where('ir_id', $id)
                    ->where('request_status', 'Approved')
                    ->first();

                    if ($reopenExists) {
                        ReopenRequest::where('respondent_id', $request->respondent_id)
                        ->where('ir_id', $id)
                        ->delete();
                    }

                } else {
                    throw new Exception($validator->messages());
                }

                /*  Cancel Retracted */
                if ($request->irr_id == 9) {
                    Respondent::where('id', $request->respondent_id)
                        ->update(
                            [
                                'type_of_date' => $request->type_of_date,
                                'start_date' => $request->start_date,
                                'takes_date' => $request->takes_date,
                                'last_date' => $request->last_date,
                            ]
                        );
                }

                if ($request->complainant_user_id && $request->respondent_user_id && $request->irr_id) {

                    /* Termination of employment update respondent table*/
                    if ($request->irr_id == 10) {
                        Respondent::where('id', $request->respondent_id)
                        ->update(
                            [
                                'first_comment' => $request->first_comment,
                                'second_comment' => $request->second_comment,
                                'mom' => $request->mom,
                            ]
                        );

                        /* Termination of employment respondent not included in email notif*/
                        $request->respondent_user_id = '';
                    }

                    EmailGeneratedIrr::dispatch(
                        $request->ir_number,
                        $request->hr_user_id,
                        $request->complainant_user_id,
                        $request->respondent_user_id
                    );
                }

                //saving data to progressionOccurrence
                $respondent = Respondent::where('id', $request->respondent_id)
                ->with(
                    'respondent_user',
                    'complainant.attendancepointssystem.category',
                    'complainant.attendancepointssystem.attendancepoint',
                    'complainant.offense.gravity',
                    'complainant.offense.category'
                )->first();
                $data = [
                    'employee_no' => $respondent->respondent_user->employee_no,
                    'last_name' => $respondent->respondent_user->last_name,
                    'first_name' => $respondent->respondent_user->first_name,
                    'gravity' => $respondent->complainant->offense ?
                        $respondent->complainant->offense->gravity->gravity : 'Minor',
                ];

                /* start NteDAClosed Saved*/
                ClosedStatusController::createNteDAClosed($request);
                ClosedStatusController::createOrUpdateInfractionMonthOrYearly($data, 'year');
                ClosedStatusController::createOrUpdateInfractionMonthOrYearly($data, 'month');
                /* end NteDAClosed Saved*/

                /* start progressionOccurence prescriptive_period_days*/
                $prescriptive_period_days = 0;
                if ($respondent->complainant->offense->gravity->prescriptive_period == "6 Months") {
                    $prescriptive_period_days = 180;

                } elseif ($respondent->complainant->offense->gravity->prescriptive_period == "12 Months") {
                    $prescriptive_period_days = 360;
                }

                if ($respondent->complainant->attendancepointssystem) {
                ProgressionOffenseController::progressionOccurenceSave($respondent, $prescriptive_period_days);
                }

                if ($respondent->complainant->offense) {
                /* start single offense progressionOccurence*/
                ProgressionOffenseController::progressionOccurenceSave($respondent, $prescriptive_period_days);

                } else {
                    /* start  offense_multiple progressionOccurence*/
                    $data['offense_ids'] = json_decode(str_replace('"', '', $respondent->complainant->offense_id));
                    $multi = [];
                    if (count($data['offense_ids']) > 1) {
                        for ($c=0; $c < count($data['offense_ids']); $c++) {
                            $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                            array_push($multi, $multiple);
                        }
                    }

                    foreach ($multi as $offense) {
                        $respondent->complainant->offense = $offense;
                    ProgressionOffenseController::progressionOccurenceSave($respondent, $prescriptive_period_days);
                    }
                    /* end offense_multiple */
                }

            } else {
                Respondent::where('id', $request->respondent_id)
                ->update(
                    [
                        'status_id' => $request->status,
                        'ir_id' => $id
                    ]
                );
            }

            /* if Status Closed, sent email */
            if ($request->status == 4 && $request->irr_id) {
                EmailClosedIncidentReport::dispatch(
                    $request->ir_number,
                    $request->hr_user_id,
                    $request->complainant_user_id,
                    $request->respondent_user_id
                );
            }

            if (json_encode($request->mom_attachment)) {
                $attach = new Attachment;
                $attach->complainant_id = $request->complainant_id;
                $attach->respondent_id = $request->respondent_id;
                $attach->attachments = json_encode($request->mom_attachment);

                if ($attach->save()) {
                    IncidentReport::where('id', $id)
                        ->where('complainant_id', $request->complainant_id)
                        ->update([
                            'minutes_of_the_meeting_attachment_id' => $attach->id,
                        ]);
                }
            }

        $this->AddEditDeleteWitness(
            $request->witness_user_id,
            $id,
            $request->hr_user_id,
            $request->complainant_user_id
        );

        $this->AddEditDeleteInvites($request->invite_user_id, $request->complainant_id, $request->hr_user_id);

        if ($request->invite_user_id && $request->ir_number &&
            $request->date_admin_hearing && $request->time_admin_hearing
            && $request->status != 4 && $request->irr_id == '') {

                $incident->invite_user_id = $request->invite_user_id;
                $incident->ir_number = $request->ir_number;
                $incident->complainant_user_id = $request->complainant_user_id;
                $incident->respondent_user_id = $request->respondent_user_id;

            EmailInvitation::dispatch(
                $incident->ir_number,
                $incident->complainant_user_id,
                $incident->respondent_user_id,
                $incident->date_admin_hearing,
                $incident->time_admin_hearing,
                $incident->invite_user_id
            );
        }

        if ($id) {
            /* reset hearing attendances when changing Date Admin Hearing */
            if ($request->date_admin_hearing || $request->time_admin_hearing || $request->meeting_place) {
                HearingAttendanceController::destroy($id);
            }
        }

        if (empty($request->witness_user_id)) {
            $request->witness_user_id = [];
        }

        if ($request->editChanges && $request->status != 4 && $request->irr_id == '') {
            EmailModifyIncidentReport::dispatch(
                $request->ir_number,
                $request->hr_user_id,
                $request->complainant_user_id,
                $request->respondent_user_id,
                $request->witness_user_id,
                $request->editChanges
            );
        }

        broadcast(new OpenClosedCasesCreated($incident));

        return $incident;
    }

    public function userInfo()
    {
        $user = auth()->user();

        return $user;
    }

    private function getEmployeeNo($employee_nos)
    {
        if (is_array($employee_nos)) {
            $employee_numbers = UsersGroup::whereIn('teamlead_employee_no', $employee_nos)
                ->select('employee_no')->get();
        } else {
            $employee_numbers = UsersGroup::where('teamlead_employee_no', $employee_nos)
                ->select('employee_no')->get();
        }

        return $employee_numbers->pluck('employee_no')->toArray();
    }

    private function getUnderManagerEmployeeID($employee_no)
    {
        $supervisor_emp_nos = $this->getEmployeeNo($employee_no);
        $employee_nos = $this->getEmployeeNo($supervisor_emp_nos);

        $employee_ids = [];
        $employee_id = User::whereIn('employee_no', $employee_nos)
            ->select('id')->get();
        foreach ($employee_id as $user) {
            if ($user->id) {
                array_push($employee_ids, $user->id);
            }
        }

        return $employee_ids;
    }

    private function getUnderSupervisorEmployeeID($employee_no): JsonResponse
    {
        $employee_nos = [];
        $employee = UsersGroup::where('teamlead_employee_no', $employee_no)
            ->select('employee_no')->get();
        foreach ($employee as $user) {
            if ($user->employee_no) {
                array_push($employee_nos, $user->employee_no);
            }
        }
        $employee_ids = [];
        $employee_id = User::whereIn('employee_no', $employee_nos)
            ->select('id')->get();
        foreach ($employee_id as $user) {
            if ($user->id) {
                array_push($employee_ids, $user->id);
            }
        }

        return response()->json($employee_ids);
    }
    public function signatoryEmployee(int $emp_no)
    {
        $user = User::where('employee_no', $emp_no)->with('designation')->first();

        return $user;
    }
    public function statusIncident(int $statusid, $return_checker = false)
    {
        ini_set('memory_limit', '4095M');
        $designation = auth()->user()->designation->name;
        $user_emp_no = auth()->user()->employee_no;
        $user_id = auth()->user()->id;
        $roles = auth()->user()->roles[0]->name;

        $status = Respondent::with([
                'hrsl_user',
                'hrbp_user.designation',
                'complainant.complainant_user',
                'progression_occurence',
                'respondent_user',
                'complainant_request.requestor_name',
                'complainant_request.approver_name',
                'onhold_request.requestor_name',
                'onhold_request.approver_name',
                'respondent_user.designation',
                'respondent_user.department',
                'respondent_user.employee_supervisor.supervisor_assign.designation',
                'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
                'complainant.witness_user.witness_fullname',
                'complainant.offense',
                'complainant.offense.category',
                'complainant.offense.gravity',
                'complainant.attendancepointssystem.attendancepoint',
                'complainant.attachments',
                'complainant.complainant_user.designation',
                'incident_report.hrbp_user',
                'incident_report.irr',
                'incident_report.invite_user',
                'incident_report.invite_user.invite_fullname',
                'incident_report.witness_user',
                'incident_report.witness_user.witness_fullname',
                'incident_report.invite_hearing.invite_ir_fullname',
                'respondent_hearing',
                'progression_occurence',
                'attached_hr',
                'respondent_attachments',
                'incident_report.ir_signatories',
                'complainant.offense.instance',
        ]);
        $status = $status->where('status_id', $statusid);
        /***compute_ageing***
         * TODO: will replace to laravel task scheduler
         * $statusid == 2 - In Progress
         * $statusid == 3 - On-Hold
         ***compute_ageing****/
        if ($statusid == 1 || $statusid == 2) {
            $this->compute_ageing($status->orderBy('ir_number', 'desc')->get());
        }

        /* 1. Super Admin Access*/
        if ($roles == "Super Admin Access") {
            $status = $status->orderBy('ir_number', 'desc')->get();
        } elseif ($roles == "HR Admin Access") { /* 2. HR Admin */
            $status = $status->where('hrsl_employee_no', $user_emp_no)
            // ->orWhere('hrbp_employee_no', $user_emp_no)
            ->orderBy('ir_number', 'desc')->get();

        } elseif ($roles == "HR Access") { /* 3. HR Access */
            $hrsl = HrbpCluster::selectRaw('DISTINCT hrsl_employee_no')
                ->where('hrbp_employee_no', $user_emp_no)
                ->get();
            $status = $status->where('hrsl_employee_no', $hrsl[0]->hrsl_employee_no)
            ->orderBy('ir_number', 'desc')->get();
            if ($statusid == 2) {
                $this->sendEmailNotify($status);
            }
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Manager') == 1) {
            /* 4. Regular Supervisor - DM */
            if (Str::contains($designation, 'Senior Operations Manager') == 1) {
                $status = $status->orderBy('ir_number', 'desc')->get();
            } elseif (Str::contains($designation, 'Resolution Manager') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['id'];
                }
                array_push($supArray, $user_id);

                $status = $status->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')->get();
            } else {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [];
                foreach ($ugFirsts as $ugFirst) {
                    $supArray[] = $ugFirst['employee_assign']['employee_no'];
                }

                $ugSeconds = UsersGroup::whereIn('teamlead_employee_no', $supArray)->get();

                $supArray2 = [];
                foreach ($ugSeconds as $ugSecond) {
                    $supArray2[] = $ugSecond['employee_assign']['id'];
                }
                array_push($supArray2, $user_id);

                $status = $status->whereIn('respondent_user_id', $supArray2)
                ->orderBy('ir_number', 'desc')->get();
            }
        } elseif ($roles == "Regular Supervisor Access" && Str::contains($designation, 'Supervisor') == 1
        ||  Str::contains($designation, 'Team Lead') == 1) {
            /* 4. Regular Supervisor - Sup*/
            if (Str::contains($designation, 'Senior Operation Supervisor') == 1) {
                $ugFirsts = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

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
                array_push($supArray3, $user_id);

                $status = $status->whereIn('respondent_user_id', $supArray3)
                ->orderBy('ir_number', 'desc')->get();
            } else {
                $userSups = UsersGroup::where('teamlead_employee_no', $user_emp_no)->get();

                $supArray = [auth()->id()];
                foreach ($userSups as $userSup) {
                    $supArray[] = $userSup['employee_assign']['id'];
                }
                array_push($supArray, $user_id);
                $status = $status->whereIn('respondent_user_id', $supArray)
                ->orderBy('ir_number', 'desc')->get();
            }
        } else { /* 5. Regular User */
            $status = $status->where('respondent_user_id', auth()->id())
            ->orderBy('ir_number', 'desc')->get();
        }
        // for searching.
        $offense = $this->getOffenseOnlyTrashed();

        $data_return = [];
        foreach ($status as $value) {
            if (empty($value->complainant->offense)) {
                $value['offense'] = $value->complainant->attendancepointssystem->type_infraction;
            } else {
                if (in_array($value->complainant->offense->id, $offense)) {
                    $value['archived'] = 'Offense Already Archived';
                }
                $value['offense'] = $value->complainant->offense->offense;
            }
            $irr_exist = $this->IncidentReportResolutionCheckIfExist($value->incident_report->initial_irr_id);
            $value['irr_exist'] = $irr_exist;
            $requestedit = ComplainantEditRequest::where('ir_id', $value->ir_id)
                ->whereIn('request_status', ['Pending', 'Approved'])
                ->first();
            $value['request_status'] = false;
            if (!empty($requestedit)) {
                $value['request_status'] = $requestedit->request_status;
            }
            $onholdRequest = OnHoldRequest::where('ir_id', $value->ir_id)
                ->whereIn('request_status', ['Pending', 'Approved'])
                ->first();
            $value['onhold_request_status'] = false;
            if (!empty($onholdRequest)) {
                $value['onhold_request_status'] = $onholdRequest->request_status;
            }
            array_push($data_return, $value);
        }
        $data_to_collection = Collection::make($data_return);
        if ($return_checker) {
            return $data_to_collection;
        } else {
            return  response()->json($data_to_collection);
        }
    }

    private function IncidentReportResolutionCheckIfExist($irr_id)
    {
        $exist = IncidentReportResolution::withTrashed()->where('id', $irr_id)->first();
        if ($exist->deleted_at) {
            $irr_exist = 'Corrective Action Already archived';
        }

        return $irr_exist;
    }

    public static function getOffenseOnlyTrashed()
    {
        $offense = Offense::onlyTrashed()->get(['id']);
        $array_offense = [];

        foreach ($offense as $value) {
            array_push($array_offense, $value->id);
        }

        return $array_offense;
    }

    public function statusIncidentCountNew(int $statusid)
    {
        $user_emp_no = auth()->user()->employee_no;
        $roles = auth()->user()->roles[0]->name;

        if ($roles == "HR Admin Access") {
            $statusHrsl = $this->statusIncident($statusid, 1);
            $statusCount = $statusHrsl->where('hrsl_employee_no', $user_emp_no);
        } elseif ($roles == "HR Access") {
            $statusHrbp = $this->statusIncident($statusid, 1);
            $statusCount = $statusHrbp->where('hrbp_employee_no', $user_emp_no);
        } elseif ($roles == "Super Admin Access") {
            $statusCount = $this->statusIncident($statusid, 1);
        }
        return $statusCount->count();
    }

    public function listNte(int $comp_id, int $res_id)
    {
        $data = Respondent::with([
            'hrsl_user.designation',
            'complainant.complainant_user',
            'respondent_user',
            'progression_occurence',
            'respondent_user.department',
            'respondent_user.designation',
            'respondent_user.employee_supervisor.supervisor_assign.designation',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
            'complainant',
            'complainant.witness_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attendancepointssystem.attendancepoint',
            'complainant.attendancepointssystem.category',
            'complainant.attachments',
            'incident_report.hrbp_user.designation',
            'incident_report.irr',
            'incident_report.initial_irr',
            'incident_report.invite_user',
            'incident_report.witness_user.witness_fullname.designation',
            'attached_hr',
        ])
        ->where('complainant_id', $comp_id)
        ->where('respondent_user_id', $res_id)
        ->first();
        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false) {
            $office = Office::where('site', 'like', '%square%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $data['work_location'] = $office->complete_address;
        }
        if (strlen($data->complainant->offense->offense) >= 295) {
            $data['long_offense_char'] = 1;
        }

        /* start  offense_multiple */
        $data['offense_ids'] = json_decode(str_replace('"', '', $data->complainant->offense_id));
        $decoded_instance = json_decode($data->incident_report->initial_instance);
        $decoded_irr = json_decode($data->incident_report->initial_irr_id);
        if (count($data['offense_ids']) > 1 && ($decoded_instance && $decoded_irr)) {
            $multi = [];
            for ($c=0; $c < count($data['offense_ids']); $c++) {
                $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                $multiple['instance'] = $decoded_instance[$c];
                $irr = IncidentReportResolution::where('id', $decoded_irr[$c])->first();
                $multiple['irr'] = $irr->name;
                if (Str::contains($irr->name, 'Termination') == true ) {
                $data['terminable'] = true;
                $data['terminable_art297'] = $this->article297Terminable($data->incident_report->id);
                }
                array_push($multi, $multiple);
            }
            $data['offense_multiple'] = $multi;
            $data['offense_multiple_new'] = true;
        } else if(count($data['offense_ids']) > 1) {
            $multi = [];
            for ($c=0; $c < count($data['offense_ids']); $c++) {
                $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                $irr = IncidentReportResolution::where('id', $decoded_irr[$c])->first();
                if (Str::contains($irr->name, 'Termination') == true) {
                $data['terminable'] = true;
                $data['terminable_art297'] = $this->article297Terminable($data->incident_report->id);
                }
                array_push($multi, $multiple);
            }
            $data['offense_multiple'] = $multi;
            $data['offense_multiple_new'] = false;
        }else {
            $irr = IncidentReportResolution::where('id', $data->incident_report->initial_irr_id)->first();
            if (Str::contains($irr->name, 'Termination') == true ) {
                $data['terminable'] = true;
                $data['terminable_art297'] = $this->article297Terminable($data->incident_report->id);
            }
            $data->complainant->offense_id;
        }
        /* end  offense_multiple */
        return $data;
    }

    public function generateNte(int $comp_id, int $res_id)
    {
        $data = $this->listNte($comp_id, $res_id);
        $filename = "NTE_".$data->ir_number
                . "_" . str_replace(' ', '_', $data->respondent_user->first_name)
                . "_". str_replace(' ', '_', $data->respondent_user->last_name)
                . "_" . $data->respondent_user->employee_no;

        if ($data->attached_hr) {
            if ($data->complainant->attachment_type >= 2) {
                $data['overHeight'] = $this->checkHeight($data->attached_hr);
                $data['overWidth'] = $this->checkWidth($data->attached_hr);
            }
            $data['file_image'] = $this->imageSetup($data->attached_hr);
        }

        $data = $this->signatoryMatrix($data);

        $pdf = PDF::loadView('pdf.generate.nte', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->download($filename.'.pdf');
        // return view('pdf.generate.nte', $data);
    }

    private function checkHeight($data)
    {
        foreach ($data as $value) {
            $data2 = json_decode($value['attachments']);
            $return_array = [];
            foreach (explode(',', $data2) as $info) {
                $file = "attachments/".$info;
                $file_exist = file_exists($file);
                if ($file_exist) {
                    $overSize = false;
                    $file_extention_array = explode('.', $file);
                    $file_extention = strtolower(end($file_extention_array));

                    $size = getimagesize($file);
                    if ($size[1] >= 650) {
                        $overHeight = true;
                    }
                    if ($size[0] >= 700) {
                        $overWidth = true;
                    }
                    array_push($return_array, $overHeight);
                }
            }

            return $return_array;
        }
    }

    private function checkWidth($data)
    {
        foreach ($data as $value) {
            $data2 = json_decode($value['attachments']);
            $return_array = [];
            foreach (explode(',', $data2) as $info) {
                // $file = storage_path('app/public/attachments/'.$info);
                $file = "attachments/".$info;
                $file_exist = file_exists($file);
                if ($file_exist) {
                    $overSize = false;
                    $file_extention_array = explode('.', $file);
                    $file_extention = strtolower(end($file_extention_array));

                    $size = getimagesize($file);

                    if ($size[0] >= 700) {
                        $overWidth = true;
                    }
                    array_push($return_array, $overWidth);
                }
            }

            return $return_array;
        }
    }

    private function imageSetup($data)
    {
        foreach ($data as $value) {
            $data2 = json_decode($value['attachments']);
            $supported_image = [
                'jpg',
                'jpeg',
                'png'
            ];
            $array_file = [];
            foreach (explode(',', $data2) as $info) {
                $file = "attachments/".$info;

                $file_exist = file_exists($file);
                if ($file_exist) {
                    $file_extention_array = explode('.', $file);
                    $file_extention = strtolower(end($file_extention_array));

                    if (in_array($file_extention, $supported_image)) {
                        array_push($array_file, $file);
                    }
                }
            }

            return $array_file;
        }
    }

    public function listIRR(int $comp_id, int $res_user_id)
    {
        $data = [];
        $respondent = Respondent::with([
            'hrsl_user.designation',
            'complainant.complainant_user',
            'progression_occurence',
            'respondent_user',
            'respondent_user.department',
            'respondent_user.designation',
            'respondent_user.employee_supervisor.supervisor_assign.designation',
            'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
            'complainant',
            'complainant.witness_user',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.offense.gravity',
            'complainant.attendancepointssystem.attendancepoint',
            'complainant.attachments',
            'incident_report.hrbp_user.designation',
            'incident_report.irr',
            'incident_report.invite_user',
            'incident_report.witness_user.witness_fullname.designation',
            'incident_report.witness_user',
            'complainant.offense.behavioral_action.first_occur',
            'complainant.offense.behavioral_action.second_occur',
            'complainant.offense.behavioral_action.third_occur',
            'complainant.offense.behavioral_action.fourth_occur',
            'complainant.offense.behavioral_action.fifth_occur',
            'complainant.offense.behavioral_action.sixth_occur',
            'complainant.offense.behavioral_action.seventh_occur',
        ])
        ->where('complainant_id', $comp_id)
        ->where('respondent_user_id', $res_user_id)
        ->first();

        $data = $respondent;
        $data->response = str_replace("<p>", "", $respondent->response);
        $data->response = str_replace("</p>", "<br>", $respondent->response);
        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false
            ) {
            $office = Office::where('site', 'like', '%square%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $data['work_location'] = $office->complete_address;
        }
        if (strlen($data->complainant->offense->offense) >= 295) {
            $data['long_offense_char'] = 1;
        }

        /* start  offense_multiple */
        $data['offense_ids'] = json_decode(str_replace('"', '', $data->complainant->offense_id));
        $data['offense_ids'] = json_decode(str_replace('"', '', $data->complainant->offense_id));
        $decoded_instance = json_decode($data->incident_report->final_instance);
        $decoded_irr = json_decode($data->incident_report->final_irr_multiple);
        $decoded_initial_irr = json_decode($data->incident_report->initial_irr_id);
        if ($data->incident_report->final_irr_single) {
            if ($decoded_initial_irr == 10 && $data->incident_report->irr_id != 10) {
                $data['in_lieu'] = true;
            }
            $irr = IncidentReportResolution::where('id', $data->incident_report->final_irr_single)->first();
            $data['final_irr_single'] = $irr->name;
        }
        if (count($data['offense_ids']) > 1 && ($decoded_instance && $decoded_irr)) {
            $multi = [];
            for ($c=0; $c < count($data['offense_ids']); $c++) {
                $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                if ($decoded_instance && $decoded_irr) {
                    if ($decoded_initial_irr[$c] == 10 && $data->incident_report->irr_id != 10) {
                        $data['in_lieu'] = true;
                    }
                    $multiple['instance'] = $decoded_instance[$c];
                    $irr = IncidentReportResolution::where('id', $decoded_irr[$c])->first();
                    $multiple['irr'] = $irr->name;
                }
                array_push($multi, $multiple);
            }
            $data['offense_multiple'] = $multi;
            $data['offense_multiple_new'] = true;
        } elseif (count($data['offense_ids']) > 1) {
            $multi = [];
            for ($c=0; $c < count($data['offense_ids']); $c++) {
                if ($decoded_initial_irr[$c] == 10 && $data->incident_report->irr_id != 10) {
                    $data['in_lieu'] = true;
                }
                $multiple = OffenseController::codeofconduct($data['offense_ids'][$c]);
                array_push($multi, $multiple);
            }
            $data['offense_multiple'] = $multi;
            $data['offense_multiple_new'] = false;
        } else {
            if ($decoded_initial_irr == 10 && $data->incident_report->irr_id != 10) {
                $data['in_lieu'] = true;
            }
            $data->complainant->offense_id;
        }
        /* end  offense_multiple */

        /* suspension date multiple */
        $suspension_date_multi =  json_decode($data->incident_report->suspension_date);
        if ($suspension_date_multi) {
            $data['suspension_date_multi_count']  = count($suspension_date_multi);

            foreach($suspension_date_multi as &$suspension_dates)
            {
                $suspension_dates = date("F d, Y", strtotime($suspension_dates));
            }
            $data['suspension_date_multi'] = implode(', ',$suspension_date_multi);
        }

        return $data;
    }

    public function generateIRR(int $comp_id, int $res_user_id)
    {
        $data = $this->listIRR($comp_id, $res_user_id);

        if (Str::contains($data->incident_report->manager_acknowledge, Carbon::now()->format("Y")) == true
        || $data->incident_report->manager_acknowledge == null) {

            // if ($data->incident_report->date_da == null) {
            //     $data = $this->updateDateDA($comp_id, $res_user_id, $data->incident_report->updated_at);
            // }

            $filename = "IRR_".$data->ir_number
                        . "_" . str_replace(' ', '_', $data->respondent_user->first_name)
                        . "_" . str_replace(' ', '_', $data->respondent_user->last_name)
                        . "_" . $data->respondent_user->employee_no;
            if ($data->attached_hr) {
                $data['file_image'] = $this->imageSetup($data->attached_hr);
            }

            $data = $this->signatoryMatrix($data);
            // replacing multiple new line to a single line
            $data->response = preg_replace('/(?:\s*<br[^>]*>\s*){3,}/s', "<br>", $data->response);

            if ($data->incident_report->irr->name == "Termination of Employment"
            || $data->incident_report->irr->name == "Dismissal") {
                $data['terminable_art297'] = $this->article297Terminable($data->incident_report->id);
                $pdf = PDF::loadView('pdf.generate.irr_termination', $data);
            } elseif ($data->incident_report->irr->name == "Absolved") {
                $pdf = PDF::loadView('pdf.generate.irr_absolved', $data);
            } else {
                $pdf = PDF::loadView('pdf.generate.irr', $data);
            }
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->download($filename.'.pdf');
            // return view('pdf.generate.irr', $data);

        } else {
            return redirect('api/admin/incidentreport/generate/irr/preview/'.$comp_id.'/'.$res_user_id)
            ->with('success','Manager required to Acknowledge this IR:#'.$data->ir_number.' before you can download!!!');
        }
    }

    public function previewNte(int $comp_id, int $res_user_id)
    {
        $data = $this->listNte($comp_id, $res_user_id);
        if ($data->incident_report->date_nte_serve == null) {
            IncidentReport::where('complainant_id', $comp_id)->update([
                'date_nte_serve' => $data->incident_report->updated_at,
            ]);
        }

        if ($data->attached_hr) {
            $data['file_image'] = $this->imageSetup($data->attached_hr);
        }

        $data = $this->signatoryMatrix($data);
        $data->response = preg_replace('/(?:\s*<br[^>]*>\s*){3,}/s', "<br>", $data->response);

        $preview = view('pdf.generate.preview.nte_prev', $data);
        return $preview;
    }

    public function previewIRR(int $comp_id, int $res_user_id)
    {
        $data = $this->listIRR($comp_id, $res_user_id);
        // if ($data->incident_report->date_da == null) {
        //     $data = $this->updateDateDA($comp_id, $res_user_id, $data->incident_report->updated_at);
        // }

        if ($data->attached_hr) {
            $data['file_image'] = $this->imageSetup($data->attached_hr);
        }

        $data = $this->signatoryMatrix($data);
        $data->response = preg_replace('/(?:\s*<br[^>]*>\s*){3,}/s', "<br>", $data->response);

        if ($data->incident_report->irr->name == "Termination of Employment"
        || $data->incident_report->irr->name == "Dismissal") {
            $data['terminable_art297'] = $this->article297Terminable($data->incident_report->id);
            $preview = view('pdf.generate.preview.irr_termination_prev', $data);
        } elseif ($data->incident_report->irr->name == "Absolved") {
            $preview = view('pdf.generate.preview.irr_absolved_prev', $data);
        } else {
            $preview = view('pdf.generate.preview.irr_prev', $data);
        }

        return $preview;
    }

    private function signatoryMatrix($data)
    {
        /* Start Input Signatory */
        $signatory = Signatory::where('ir_id', $data['incident_report']['id'])
        ->whereNotNull('prepared_by_first')
        ->whereNotNull('prepared_by_last')
        ->whereNotNull('prepared_by_designation')
        ->whereNotNull('prepared_by_empno')
        ->whereNotNull('requested_by_first')
        ->whereNotNull('requested_by_last')
        ->whereNotNull('requested_by_designation')
        ->whereNotNull('requested_by_empno')
        ->get();

        if (count($signatory) > 0) {
            if ($signatory[0]['prepared_by_first'] && $signatory[0]['prepared_by_last']
            && $signatory[0]['prepared_by_designation'] && $signatory[0]['prepared_by_empno']) {

                $data['prepared_by_input'] = true;
                $data['prepared_by_input_designation'] = $signatory[0]['prepared_by_designation'];
                $data['prepared_by_input_empno'] = $signatory[0]['prepared_by_empno'];
                $data['prepared_by_input_fullname'] = $signatory[0]['prepared_by_first'] . " "
                                                    . $signatory[0]['prepared_by_last'];

            }

            if ($signatory[0]['requested_by_first'] && $signatory[0]['requested_by_last']
            && $signatory[0]['requested_by_designation'] && $signatory[0]['requested_by_empno']) {

                $data['requested_by_input'] = true;
                $data['requested_by_input_designation'] = $signatory[0]['requested_by_designation'];
                $data['requested_by_input_empno'] = $signatory[0]['requested_by_empno'];
                $data['requested_by_input_fullname'] = $signatory[0]['requested_by_first'] . " "
                                                    . $signatory[0]['requested_by_last'];
            }

            if ($signatory[0]['approved_by_first'] && $signatory[0]['approved_by_last']
            && $signatory[0]['approved_by_designation'] && $signatory[0]['approved_by_empno']) {

                $data['approved_by_input'] = true;
                $data['approved_by_input_designation'] = $signatory[0]['approved_by_designation'];
                $data['approved_by_input_empno'] = $signatory[0]['approved_by_empno'];
                $data['approved_by_input_fullname'] = $signatory[0]['approved_by_first'] . " "
                                                    . $signatory[0]['approved_by_last'];
            }

            if ($signatory[0]['noted1_by_first'] && $signatory[0]['noted1_by_last']
            && $signatory[0]['noted1_by_designation'] && $signatory[0]['noted1_by_empno']) {

                $data['noted1_by_input'] = true;
                $data['noted1_by_input_designation'] = $signatory[0]['noted1_by_designation'];
                $data['noted1_by_input_empno'] = $signatory[0]['noted1_by_empno'];
                $data['noted1_by_input_fullname'] = $signatory[0]['noted1_by_first'] . " "
                                                    . $signatory[0]['noted1_by_last'];
            }

            if ($signatory[0]['noted2_by_first'] && $signatory[0]['noted2_by_last']
            && $signatory[0]['noted2_by_designation'] && $signatory[0]['noted2_by_empno']) {

                $data['noted2_by_input'] = true;
                $data['noted2_by_input_designation'] = $signatory[0]['noted2_by_designation'];
                $data['noted2_by_input_empno'] = $signatory[0]['noted2_by_empno'];
                $data['noted2_by_input_fullname'] = $signatory[0]['noted2_by_first'] . " "
                                                    . $signatory[0]['noted2_by_last'];
            }

            if ($signatory[0]['noted3_by_first'] && $signatory[0]['noted3_by_last']
            && $signatory[0]['noted3_by_designation'] && $signatory[0]['noted3_by_empno']) {

                $data['noted3_by_input'] = true;
                $data['noted3_by_input_designation'] = $signatory[0]['noted3_by_designation'];
                $data['noted3_by_input_empno'] = $signatory[0]['noted3_by_empno'];
                $data['noted3_by_input_fullname'] = $signatory[0]['noted3_by_first'] . " "
                                                    . $signatory[0]['noted3_by_last'];
            }
            /* End Input Signatory */
        } elseif (count($signatory) == 0) {

            /************************************************
             * Rank and File
             * Underwriting, Quality
             * To be Commented out for manually input
             * *********************************************/

            if ((Str::contains($data->respondent_user->designation->name, 'Underwriting') == true
            || Str::contains($data->respondent_user->designation->name, 'Underwriter') == true
            || Str::contains($data->respondent_user->designation->name, 'Quality') == true)
            && (Str::contains($data->respondent_user->designation->name, 'Supervisor') == false
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == false
            || Str::contains($data->respondent_user->designation->name, 'Manager') == false)) {
                /* Rank and File - Underwriting, Quality (Termination, Dismissal) */
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                    $data['noted_by_hr_manager'] = true;
                } else {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                }
            }

            /************************************************
             * Supervisor
             * Underwriting
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Supervisor') == true
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == true)
            && Str::contains($data->respondent_user->designation->name, 'Underwriting') == true) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_head_uw'] = true;
                    $data['noted_by_hr_manager'] = true;
                    $data['noted_by_hr_director'] = true;
                } else {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_head_uw'] = true;
                }
            }

            /************************************************
             * Manager
             * Underwriting
             * *********************************************/
            if (Str::contains($data->respondent_user->designation->name, 'Underwriting') == true
                && Str::contains($data->respondent_user->designation->name, 'Manager') == true) {
                    $data['prepared_by_hr_manager'] = true;
                    $data['requested_by_head_uw'] = true;
                    $data['noted_by_hr_director'] = true;
            }

            /************************************************
             * Supervisor
             * Quality, Collection
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Quality') == true
            || Str::contains($data->respondent_user->designation->name, 'Collection') == true)
            && (Str::contains($data->respondent_user->designation->name, 'Supervisor') == true
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == true)) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_coo'] = true;
                    $data['noted_by_hr_manager'] = true;
                    $data['noted_by_hr_director'] = true;
                } else {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_hr_manager'] = true;
                }
            }

            /************************************************
             * Manager
             * Quality
             * *********************************************/
            if (Str::contains($data->respondent_user->designation->name, 'Quality') == true
                && Str::contains($data->respondent_user->designation->name, 'Manager') == true) {
                    $data['prepared_by_hr_manager'] = true;
                    $data['requested_by_coo'] = true;
                    $data['noted_by_hr_director'] = true;
            }

            /************************************************
             * Rank and File
             * Collection
             * Minor, Major, Serious
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Supervisor') == false
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == false
            || Str::contains($data->respondent_user->designation->name, 'Manager') == false)
            && (Str::contains($data->respondent_user->designation->name, 'Collection') == true)) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                    $data['noted_by_hr_manager'] = true;
                } else {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                }
            }

            /************************************************
             * Manager
             * Collection
             * *********************************************/
            if (Str::contains($data->respondent_user->designation->name, 'Collection') == true
                && Str::contains($data->respondent_user->designation->name, 'Manager') == true) {
                    $data['prepared_by_hr_manager'] = true;
                    $data['requested_by_coo'] = true;
                    $data['noted_by_hr_director'] = true;
            }

            /************************************************
             * Rank and File
             * Loan
             * Commented out for manually input
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Supervisor') == false
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == false
            || Str::contains($data->respondent_user->designation->name, 'Manager') == false)
            && (Str::contains($data->respondent_user->designation->name, 'Loan') == true
            || Str::contains($data->respondent_user->designation->name, 'Agent') == true)) {
                /* Termination, Dismissal */
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                    $data['noted_by_hr_manager'] = true;
                    $data['noted_by_som'] = true;
                    $data['agent_grave'] = "agent_grave";
                } else {
                    $data['prepared_by_hrbp'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                    $data['agent_minor'] = "agent_minor";
                }
            }

            /************************************************
             * Supervisor
             * Operation
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Operations Supervisor') == true
            || Str::contains($data->respondent_user->designation->name, 'Operation Supervisor') == true)) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_som'] = true;
                    $data['noted_by_gm'] = true;
                    $data['noted_by_hr_manager'] = true;
                    $data['noted_by_hr_director'] = true;
                } else {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['approved_by_som'] = true;
                }
            }

            /************************************************
             * Manager
             * Operation
             * Minor, Major, Serious
             * *********************************************/
            if (Str::contains($data->respondent_user->designation->name, 'Operation') == true
            && Str::contains($data->respondent_user->designation->name, 'Manager') == true) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hr_manager'] = true;
                    $data['requested_by_som'] = true;
                    $data['approved_by_gm'] = true;
                    $data['noted_by_hr_director'] = true;
                    $data['noted_by_coo'] = true;
                } else {
                    $data['prepared_by_hr_manager'] = true;
                    $data['requested_by_som'] = true;
                    $data['approved_by_gm'] = true;
                }
            }

            /************************************************
             * Rank and File
             * Shared Services
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Supervisor') == false
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == false
            || Str::contains($data->respondent_user->designation->name, 'Manager') == false)
            && (Str::contains($data->respondent_user->designation->name, 'Underwriting') == false
            && Str::contains($data->respondent_user->designation->name, 'Quality') == false
            && Str::contains($data->respondent_user->designation->name, 'Collection') == false
            && Str::contains($data->respondent_user->designation->name, 'Operation') == false
            && Str::contains($data->respondent_user->designation->name, 'Loan') == false
            && Str::contains($data->respondent_user->designation->name, 'Agent') == false)) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                    $data['noted_by_hr_manager'] = true;
                } else {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_supervisor'] = true;
                    $data['approved_by_manager'] = true;
                }
            }

            /************************************************
             * Supervisor
             * Shared Service
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Supervisor') == true
            || Str::contains($data->respondent_user->designation->name, 'Team Lead') == true)
            && (Str::contains($data->respondent_user->designation->name, 'Underwriting') == false
            && Str::contains($data->respondent_user->designation->name, 'Quality') == false
            && Str::contains($data->respondent_user->designation->name, 'Collection') == false
            && Str::contains($data->respondent_user->designation->name, 'Operation') == false)) {
                /*Termination, Dismissal*/
                if (Str::contains($data->incident_report->initial_irr->name, "Termination") == true
                || Str::contains($data->incident_report->irr->name, "Termination") == true
                || Str::contains($data->incident_report->initial_irr->name, "Dismissal") == true
                || Str::contains($data->incident_report->irr->name, "Dismissal") == true) {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                    $data['noted_by_hr_manager'] = true;
                    $data['noted_by_hr_director'] = true;
                } else {
                    $data['prepared_by_hrsl'] = true;
                    $data['requested_by_manager'] = true;
                }
            }

            /************************************************
             * Manager
             * Shared Service
             * *********************************************/
            if ((Str::contains($data->respondent_user->designation->name, 'Underwriting') == false
                && Str::contains($data->respondent_user->designation->name, 'Quality') == false
                && Str::contains($data->respondent_user->designation->name, 'Collection') == false
                && Str::contains($data->respondent_user->designation->name, 'Operation') == false)
                && Str::contains($data->respondent_user->designation->name, 'Manager') == true) {
                    $data['prepared_by_hr_manager'] = true;
                    $data['noted_by_hr_director'] = true;
            }

            /************************************************
             * Call Position
             * *********************************************/
            $data['office_location'] = auth()->user()->office_location->name;

            if (Str::contains(strtolower($data['office_location']), "square") == 1
            || Str::contains(strtolower($data['office_location']), "bgc") == 1
            || Str::contains(strtolower($data['office_location']), "market") == 1
            || Str::contains(strtolower($data['office_location']), "alabang") == 1) {
                /* HR Manager */
                if ($data['approved_by_hr_manager'] || $data['noted_by_hr_manager'] || $data['prepared_by_hr_manager']) {
                    /***************************************************************
                     * TODO: if Jenna got replacement as HR Site Lead will replace her designation ID
                     * Temporary set to Jenna as HR Manager
                     * *************************************************************/
                    $data['hr_manager_fullname'] = "Jenna Tamsine Leonardo";
                    $data['hr_manager_employee_no'] = "20182869";
                    $data['hr_manager_designation'] = "HR Manager";
                }

                /* HR Director */
                if ($data['noted_by_hr_director']) {
                    $hr_director = User::with(['designation'])->whereHas('designation', function ($query) {
                        $query->where('name', 'LIKE', '%HR Director%');
                    })->first();

                    $data['hr_director_fullname'] = $hr_director['first_name']." ".$hr_director['last_name'];
                    $data['hr_director_employee_no'] = $hr_director['employee_no'];
                    $data['hr_director_designation'] = $hr_director['designation']['name'];
                }

                /* General Manager */
                if ($data['noted_by_gm'] || $data['approved_by_gm']) {
                    $gm = User::with(['designation'])->whereHas('designation', function ($query) {
                        $query->where('name', 'General Manager');
                    })->first();

                    $data['gm_fullname'] = $gm['first_name']." ".$gm['last_name'];
                    $data['gm_employee_no'] = $gm['employee_no'];
                    $data['gm_designation'] = $gm['designation']['name'];
                }
                /*******************************
                 * Senior Operations Manager
                 * 20160711 - Christian Alpizar
                *******************************/
                if ($data['noted_by_som'] || $data['approved_by_som'] || $data['requested_by_som']) {
                    $som = User::with(['designation'])->whereHas('designation', function ($query) {
                        $query->where('name', 'Senior Operations Manager');
                    })
                    ->where('employee_no', '20160711')
                    ->first();

                    $data['som_fullname'] = $som['first_name']." ".$som['last_name'];
                    $data['som_employee_no'] = $som['employee_no'];
                    $data['som_designation'] = $som['designation']['name'];
                }

                /* Underwriting Department Head */
                if ($data['approved_by_head_uw'] || $data['requested_by_head_uw']) {
                    $uw_head = User::with(['designation'])->whereHas('designation', function ($query) {
                        $query->where('name', 'LIKE', '%Underwriting%');
                        $query->where('name', 'LIKE', '%Head%');
                    })->first();

                    $data['uw_head_fullname'] = $uw_head['first_name']." ".$uw_head['last_name'];
                    $data['uw_head_employee_no'] = $uw_head['employee_no'];
                    $data['uw_head_designation'] = $uw_head['designation']['name'];
                }

                /* Chief Operations Officer */
                if ($data['approved_by_coo'] || $data['requested_by_coo'] || $data['noted_by_coo']) {
                    $cd = User::with(['designation'])->whereHas('designation', function ($query) {
                        $query->where('name', 'Chief Operations Officer');
                    })->first();

                    $data['coo_fullname'] = $cd['first_name']." ".$cd['last_name'];
                    $data['coo_employee_no'] = $cd['employee_no'];
                    $data['coo_designation'] = $cd['designation']['name'];
                }
            }
            /* End Default Signatory */
        }

        return $data;
    }

    // private function updateDateDA(int $comp_id, int $res_user_id, $date)
    // {
    //     $incident_report = IncidentReport::where('complainant_id', $comp_id)->update([
    //         'date_da' => $date,
    //     ]);

    //     $respondent = Respondent::with([
    //         'complainant.complainant_user',
    //         'progression_occurence',
    //         'respondent_user',
    //         'respondent_user.department',
    //         'respondent_user.designation',
    //         'respondent_user.employee_supervisor.supervisor_assign.designation',
    //         'respondent_user.employee_supervisor.supervisor_assign.employee_supervisor.supervisor_assign.designation',
    //         'complainant',
    //         'complainant.witness_user',
    //         'complainant.offense',
    //         'complainant.offense.category',
    //         'complainant.offense.gravity',
    //         'complainant.attendancepointssystem.attendancepoint',
    //         'complainant.attachments',
    //         'incident_report.hrbp_user.designation',
    //         'incident_report.irr',
    //         'incident_report.invite_user',
    //         'incident_report.witness_user.witness_fullname.designation',
    //         'incident_report.witness_user',
    //         'complainant.offense.behavioral_action.first_occur',
    //         'complainant.offense.behavioral_action.second_occur',
    //         'complainant.offense.behavioral_action.third_occur',
    //         'complainant.offense.behavioral_action.fourth_occur',
    //         'complainant.offense.behavioral_action.fifth_occur',
    //         'complainant.offense.behavioral_action.sixth_occur',
    //         'complainant.offense.behavioral_action.seventh_occur',
    //         // 'progression_offense.po_schedule_adherence',
    //         // 'progression_offense.po_performance_related',
    //         // 'progression_offense.po_behaviour_related',
    //         // 'progression_offense.po_information_security',
    //         // 'progression_offense.po_breach_trust',
    //         // 'progression_offense.po_gross_negligence',
    //     ])
    //     ->where('complainant_id', $comp_id)
    //     ->where('respondent_user_id', $res_user_id)
    //     ->first();

    //     return $respondent;
    // }

    public function generateComplainant(int $comp_id, int $res_id)
    {
        $data = $this->listNte($comp_id, $res_id);
        $data->user_id = auth()->id();
        $data->user_role = auth()->user()->roles[0]->name;

        $filename = "Complainant_".$data->ir_number
                . "_" . str_replace(' ', '_', $data->respondent_user->first_name)
                . "_". str_replace(' ', '_', $data->respondent_user->last_name)
                . "_" . $data->respondent_user->employee_no;
        $data->response = preg_replace('/(?:\s*<br[^>]*>\s*){3,}/s', "<br>", $data->response);

        if ($data->complainant->attachments) {
            $data['overHeight'] = $this->checkHeight($data->complainant->attachments);
            $data['overWidth'] = $this->checkWidth($data->complainant->attachments);
            $data['complainant_images'] = $this->imageSetup($data->complainant->attachments);
        }

        $pdf = PDF::loadView('pdf.generate.complainant', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->download($filename.'.pdf');
        // return view('pdf.generate.complainant', $data);
    }


    public function generateAdminHearing(int $comp_id, int $res_id)
    {
        $data = $this->listIRR($comp_id, $res_id);
        $data->user_id = auth()->id();
        $data->user_role = auth()->user()->roles[0]->name;
        $data->incident_report['date_admin_hearing'] =
            date('F d, Y', strtotime($data->incident_report['date_admin_hearing']));
        $filename = "AdminHearing_".$data->ir_number
                . "_" . str_replace(' ', '_', $data->respondent_user->first_name)
                . "_". str_replace(' ', '_', $data->respondent_user->last_name)
                . "_" . $data->respondent_user->employee_no;
        $data = $this->signatoryMatrix($data);

        $pdf = PDF::loadView('pdf.generate.admin_hearing', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->download($filename.'.pdf');
    }

    private function sendEmailNotify($status)
    {
        $count = [];
        foreach ($status as $value) {
            if ($value->response == null && $value->date_response == null) {
                if ($value->email_acknowledge_date != null) {
                    $from = Carbon::parse($value->email_acknowledge_date)->format("Y/m/d");
                } else {
                    $from = Carbon::parse($value->incident_report->created_at)->format("Y/m/d");
                }
                $to = Carbon::now()->format("Y/m/d");
                $working_days = $this->number_of_working_days($from, $to);
                $sent = RespondentNotResponse::where('ir_id', $value->incident_report->id)
                    ->first();
                if ((($working_days >= 6 && $working_days <= 8) && empty($sent) && count($count) <= 3)
                    && ($value->response == null && $value->date_response == null)) {
                    if ($value->ir_number) {
                        $hrbp = $value->hrbp_user->email_address != null ? $value->hrbp_user->email_address : '';

                        $respondent = $value->respondent_user->email_address != null ?
                            $value->respondent_user->email_address : '';

                        $supervisor = ($value->respondent_user->employee_supervisor != null && $value->respondent_user
                            ->employee_supervisor->supervisor_assign->email_address != null) ?
                            $value->respondent_user->employee_supervisor->supervisor_assign->email_address : '';

                        $manager_email = ($value->respondent_user->employee_supervisor->supervisor_assign
                            ->employee_supervisor != null && $value->respondent_user
                            ->employee_supervisor->supervisor_assign->employee_supervisor
                            ->supervisor_assign->email_address != null) ? $value->respondent_user
                            ->employee_supervisor->supervisor_assign->employee_supervisor
                            ->supervisor_assign->email_address : '';
                        $this->checkingEmailNullRespondent(
                            $value->ir_id,
                            $respondent,
                            $value->respondent_user->id,
                            $value->ir_number
                        );
                        $this->checkingEmailNull(
                            $value->ir_id,
                            $hrbp,
                            $value->hrbp_user->id,
                            $value->ir_number,
                            $from
                        );
                        $this->checkingEmailNull(
                            $value->ir_id,
                            $supervisor,
                            $value->respondent_user->employee_supervisor->supervisor_assign->id,
                            $value->ir_number,
                            $from
                        );
                        $this->checkingEmailNull(
                            $value->ir_id,
                            $manager_email,
                            $value->respondent_user->employee_supervisor->supervisor_assign->employee_supervisor
                                ->supervisor_assign->id,
                            $value->ir_number,
                            $from
                        );

                        array_push($count, 'ENRR');
                    }
                }
            }
        }
    }

    private function checkingEmailNullRespondent($ir_id, $email, $user_id, $ir_number)
    {
        if ($email != '') {
            $this->notReplyEmailRespondent(
                $ir_id,
                $email,
                $user_id,
                $ir_number
            );
        } else {
            $this->saveNotReplyEmail($ir_id, null, $user_id);
        }
    }

    private function notReplyEmailRespondent($ir_id, $email, $user_id, $ir_number)
    {
        EmailNoReplyRespondent::dispatch(
            $ir_number,
            $email
        );

        $this->saveNotReplyEmail($ir_id, $email, $user_id);
    }

    private function notReplyEmail($ir_id, $email, $user_id, $ir_number, $date)
    {
        EmailNoReplyOthers::dispatch(
            $ir_number,
            $email,
            $date
        );

        $this->saveNotReplyEmail($ir_id, $email, $user_id);
    }


    private function checkingEmailNull($ir_id, $email, $user_id, $ir_number, $date)
    {
        if ($email != '') {
            $this->notReplyEmail(
                $ir_id,
                $email,
                $user_id,
                $ir_number,
                $date
            );
        } else {
            $this->saveNotReplyEmail($ir_id, null, $user_id);
        }
    }

    public static function replyEmail($ir_id, $email, $date)
    {
        EmailReplyRespondent::dispatch(
            $ir_id,
            $email,
            $date
        );
    }

    private function saveNotReplyEmail($ir_id, $email, $user_id)
    {
        $sent_save = new RespondentNotResponse;
        $sent_save->ir_id = $ir_id;
        $sent_save->user_id = $user_id;
        $sent_save->email_address = $email;
        $sent_save->save();
    }

    public static function compute_ageing($status)
    {
        foreach ($status as $stat) {

            /* Stop TAT ageing when already in reopen */
            $reopen = ReopenRequest::
            where('respondent_id', $stat['id'])
            ->where('ir_id', $stat['ir_id'])
            ->where('ir_number', $stat['ir_number'])
            ->first();

            if ($reopen['ir_number'] != $stat['ir_number']) {
                $to = Carbon::now()->format("Y/m/d");

                $from = null;
                if ($stat->new_ageing == 1) {
                    if ($stat['email_acknowledge_date'] != null) {
                        $from = Carbon::parse($stat['email_acknowledge_date'])->format("Y/m/d");
                    }
                } else {
                    $from = Carbon::parse($stat['created_at'])->format("Y/m/d");
                }
                $inc_id = $stat['id'];
                $compute_ageing = Self::number_of_working_days($from, $to);
                $working_days = is_null($from) ? null : $compute_ageing;

                if ($working_days && $working_days <= 100) {
                    Respondent::where('id', $inc_id)->update(['ageing' => $working_days]);
                }
            }
        }
    }

    private static function number_of_working_days($from, $to)
    {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = [
            '*/01/01', '*/03/29', '*/03/30', '*/04/01', '*/06/12',
            '*/08/27', '*/11/30', '*/08/27', '*/12/25', '*/12/30'
        ]; # variable and fixed holidays

        // $from = new DateTime($from);
        // $to = new DateTime($to);
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);
        $to->modify('+1 day');
        // $to->modify('-10 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) {
                continue;
            }

            if (in_array($period->format('Y/m/d'), $holidayDays)) {
                continue;
            }

            if (in_array($period->format('*/m/d'), $holidayDays)) {
                continue;
            }

            $days++;
        }
        return $days;
    }

    private function AddEditDeleteWitness($request_witness_user_id, $ir_id, $request_hr_user_id, $complainant_user_id)
    {
        if ($request_witness_user_id) {
            $witness_id = Witness::where('ir_id', $ir_id)
                ->where('hr_user_id', $request_hr_user_id)
                ->get(['id', 'witness_user_id']);

            $wit_ids = [];
            $wit_user = [];
            foreach ($witness_id as $witness) {
                $wit_ids[] = $witness['id'];
                $wit_user[] = $witness['witness_user_id'];
            }

            $wit = [];
            foreach ($request_witness_user_id as $witness_user) {
                $wit[] = $witness_user;
            }

            if (empty($wit_user)) {
                foreach ($request_witness_user_id as $witness) {
                    Witness::create([
                        'ir_id' => $ir_id,
                        'complainant_user_id' => (int)$complainant_user_id,
                        'hr_user_id' => (int)$request_hr_user_id,
                        'witness_user_id' => $witness
                    ]);
                }
            } else {
                $wit_add = array_diff($wit, $wit_user);
                $wit_delete = array_diff($wit_user, $wit);

                if (!empty($wit_add)) {
                    foreach ($wit_add as $add_witness) {
                        $witness = new Witness;
                        $witness->ir_id = $ir_id;
                        $witness->complainant_user_id = $complainant_user_id;
                        $witness->hr_user_id = $request_hr_user_id;
                        $witness->witness_user_id = $add_witness;
                        $witness->save();
                    }
                }
                if (!empty($wit_delete)) {
                    foreach ($wit_delete as $delete_witness) {
                        Witness::where('ir_id', $ir_id)
                            ->where('hr_user_id', (int)$request_hr_user_id)
                            ->where('witness_user_id', (int)$delete_witness)
                            ->delete();
                    }
                }
            }

            // if (count($wit) == count($wit_user)) {
            //     $wit_update = [];
            //     foreach ($wit_ids as $index => $wit_id) {
            //         $wit_update[] = $wit[$index];
            //
            //         Witness::where('id', $wit_id)
            //             ->update(['witness_user_id' => $wit[$index]]);
            //     }
            // } elseif (count($wit) > count($wit_user)) {
            //     $wit_add = array_diff($wit, $wit_user);
            //     foreach ($wit_add as $add_witness) {
            //         $witness = new Witness;
            //         $witness->complainant_id = $request_complainant_id;
            //         $witness->hr_user_id = $request_hr_user_id;
            //         $witness->witness_user_id = $add_witness;
            //         $witness->save();
            //     }
            // } elseif (count($wit) < count($wit_user)) {
            //     $wit_delete = array_diff($wit_user, $wit);
            //     if ($wit_delete) {
            //         foreach ($wit_delete as $delete_witness) {
            //             Witness::where('complainant_id', $request_complainant_id)
            //                 ->where('hr_user_id', $request_hr_user_id)
            //                 ->where('witness_user_id', $delete_witness)
            //                 ->delete();
            //         }
            //     }
            // }
        } else {
            $status = Witness::where('ir_id', $ir_id)
            ->where('complainant_user_id', (int)$complainant_user_id)
            ->where('hr_user_id', (int)$request_hr_user_id)
            ->delete();
        }
    }

    private function AddEditDeleteInvites($request_invite_user_id, $request_complainant_id, $request_hr_user_id)
    {
        if ($request_invite_user_id) {
            $invite_id = Invite::where('complainant_id', $request_complainant_id)
                ->where('hr_user_id', $request_hr_user_id)
                ->get(['id', 'invite_user_id']);

            $inv_ids = [];
            $inv_user = [];
            foreach ($invite_id as $invite) {
                $inv_ids[] = $invite['id'];
                $inv_user[] = $invite['invite_user_id'];
            }

            $inv = [];
            foreach ($request_invite_user_id as $invite_user) {
                $inv[] = $invite_user;
            }

            if (count($inv) == count($inv_user)) {
                $inv_update = [];
                foreach ($inv_ids as $index => $inv_id) {
                    $inv_update[] = $inv[$index];

                    Invite::where('id', $inv_id)
                        ->update(['invite_user_id' => $inv[$index]]);
                }
            } elseif (count($inv) > count($inv_user)) {
                $inv_add = array_diff($inv, $inv_user);
                foreach ($inv_add as $add_invite) {
                    $invite = new Invite;
                    $invite->complainant_id = $request_complainant_id;
                    $invite->hr_user_id = $request_hr_user_id;
                    $invite->invite_user_id = $add_invite;
                    $invite->save();
                }
            } elseif (count($inv) < count($inv_user)) {
                $inv_delete = array_diff($inv_user, $inv);
                if ($inv_delete) {
                    foreach ($inv_delete as $delete_invite) {
                        Invite::where('complainant_id', $request_complainant_id)
                            ->where('hr_user_id', $request_hr_user_id)
                            ->where('invite_user_id', $delete_invite)
                            ->delete();
                    }
                }
            }
        }
    }

    private function send($emailDetails, $emailReciever)
    {
        $envMail = config('mail.username');

        Mail::send(['text' => 'email.hr'], $emailDetails, function ($message) use ($emailReciever, $envMail) {
            $message->to($emailReciever)->subject("New message from EP");
            $message->from($envMail, 'EP - Do not reply');
        });
    }

    public function sendRespondent($emailDetails, $emailRespondent)
    {
        $envMail = config('mail.username');

        Mail::send(['text' => 'email.respondent'], $emailDetails, function ($message) use ($emailRespondent, $envMail) {
            $message->to($emailRespondent)->subject('Attention: New message from EP');
            $message->from($envMail, 'EP - Do not reply');
        });
    }

    public function attachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/hr/'
                . auth()->user()->employee_no
                . '/' . Carbon::today()->format('Y-m-d')
                . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'hr/'
                    . auth()->user()->employee_no . '/'
                    . Carbon::today()->format('Y-m-d')
                    . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded]);
        }
    }

    public function momAttachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/mom/'
                . auth()->user()->employee_no
                . '/' . Carbon::today()->format('Y-m-d')
                . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'mom/'
                    . auth()->user()->employee_no . '/'
                    . Carbon::today()->format('Y-m-d')
                    . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded]);
        }
    }

    public function downloadAttachment($folder, $emp_no, $date, $filename)
    {
        return response()->download(storage_path("app/public/attachments/{$folder}/{$emp_no}/{$date}/{$filename}"));
    }

    public function downloadMoMAttachment($folder, $emp_no, $date, $filename)
    {
        return response()->download(storage_path("app/public/attachments/{$folder}/{$emp_no}/{$date}/{$filename}"));
    }

    public function headOfficerListNte(): JsonResponse
    {
        $user_emp_no = auth()->user()->employee_no;

        $status = Respondent::with([
            'hrbp_user',
            'complainant.complainant_user',
            'respondent_user',
            'respondent_user.employee_supervisor',
            'complainant.witness_user.witness_fullname',
            'complainant.offense',
            'complainant.offense.category',
            'complainant.attachments',
            'complainant.offense.gravity',
            'incident_report.hrbp_user',
            'incident_report.irr',
            'incident_report.invite_user',
            'incident_report.invite_user.invite_fullname',
            'incident_report.witness_user',
            'incident_report.witness_user.witness_fullname',
            'incident_report.irr',
            'incident_report.invite_hearing.invite_ir_fullname',
            'respondent_hearing',
            'attached_hr',
            'signed'
        ])
        ->whereHas('respondent_user.employee_supervisor', function ($query) use ($user_emp_no) {
            $query->where('teamlead_employee_no', $user_emp_no);
        })
        ->whereHas('incident_report', function ($query) {
            $query->where('is_generate_nte_invalid_ir', 1);
        })
        ->where('status_id', '!=', 1)
        ->where('status_id', '!=', 4)
        ->orderBy('created_at', 'desc')->get();

        return response()->json($status);
    }

    public function createHeadOfficerApproval(Request $request)
    {

        $supervisor = new SupervisorApproval;
        $supervisor->supervisor_user_id = $request->supervisor_user_id;
        $supervisor->respondent_user_id = $request->respondent_user_id;
        $supervisor->complainant_id = $request->complainant_id;
        $supervisor->ir_id = $request->ir_id;
        $supervisor->approver_status = $request->approver_status;
        $supervisor->save();

        // $supervisor = SupervisorApproval::create([
        //     'supervisor_user_id' => $request->supervisor_user_id,
        //     'respondent_user_id' => $request->respondent_user_id,
        //     'complainant_id' => $request->complainant_id,
        //     'ir_id' => $request->ir_id,
        //     'approver_status' => $request->approver_status
        // ]);

        return $supervisor;
    }

    public function downloadForm(string $form_type, int $ir_id, string $ir_no)
    {
        $HrManager = User::whereHas('designation', function ($query) {
            $query->where('name', 'like', '%Senior Manager Human Capital%');
        })->first();

        $respondent = Respondent::where('ir_number', $ir_no)->with('respondent_user.address')->first();
        $ir = IncidentReport::with(['hrbp_user', 'hr_attachments'])->where('id', $ir_id)->first();

        $data = [
            'ir_number' => $ir_no,
            'res_fullname' => $respondent->respondent_user->first_name." ".$respondent->respondent_user->last_name,
            'res_emp_no' => $respondent->respondent_user->employee_no,
            'res_address' => $respondent->respondent_user->address->current_address,
            'hrbp_name' => $ir->hrbp_user->first_name." ".$ir->hrbp_user->last_name,
            'hrbp_emp_no' => $ir->hrbp_user->employee_no,
            'hrm_name' => $HrManager->first_name." ".$HrManager->last_name,
            'hrm_emp_no' => $HrManager->employee_no,
            'rtw_date' => $ir->rtw_date,
            'rtw_advice_date' => $ir->rtw_advice_date,
            'sn_termination_date' => $ir->sn_termination_date,
            'sn_date_absence_start' => $ir->sn_date_absence_start,
            'sn_date_absence_end' => $ir->sn_date_absence_end,
            'hr_attachments' => preg_replace('/[^A-Za-z0-9\/._-]/', '', $ir->hr_attachments->attachments)
        ];

        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false) {
            $office = Office::where('site', 'like', '%square%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $data['work_location'] = $office->complete_address;
        }

        $filename = $form_type."_".$data['ir_number']
                    . "_" . str_replace(' ', '_', $data['res_fullname'])
                    . "_" . $data['res_emp_no'];

        $pdf = PDF::loadView('pdf.download.' . strtolower($form_type), $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        // return view('pdf.download.rtw', $data);
        // return view('pdf.download.sn', $data);
        return $pdf->download($filename.'.pdf');
    }

    public function downloadHrInterventionDocument($id, $agreement, $ir_id, $irr_id)
    {
        if ($agreement) {
            $incident = IncidentReport::where('id', $ir_id)
            ->update(
                [
                    'agreement' => $agreement,
                    'irr_id' => $irr_id
                ]
            );
        }
        $respondent = Respondent::with([
            'respondent_user',
            'respondent_user.department',
            'respondent_user.employee_supervisor.supervisor_assign',
            'respondent_user.employee_supervisor.supervisor_assign.designation',
            'respondent_user.designation',
            'complainant.offense',
            'incident_report.hrbp_user',
            'incident_report.hrbp_user.designation',
        ])
        ->where('id', $id)
        ->first();
        $HrManager = User::whereHas('designation', function ($query) {
            $query->where('name', 'like', '%Senior Manager Human Capital%');
        })->first();
        $date_start = $respondent->complainant->date_incident_start ?
            $respondent->complainant->date_incident_start : "";
        $date_end = $respondent->complainant->date_incident_end ? $respondent->complainant->date_incident_end : "";
        $data = [
            'agreement' => ($agreement) ? $agreement : '',
            'ir_number' => $respondent->ir_number,
            'date_transpired' => $date_start . " - " . $date_end,
            'offense' => $respondent->complainant->offense->offense,
            'supervisor_name' => $respondent->respondent_user->employee_supervisor->supervisor_assign->first_name
            . " " . $respondent->respondent_user->employee_supervisor->supervisor_assign->last_name,
            'supervisor_emp_id' => $respondent->respondent_user->employee_supervisor->supervisor_assign->employee_no,
            'supervisor_position' => $respondent->respondent_user->employee_supervisor->supervisor_assign
                ->designation->name,
            'res_full_name' => $respondent->respondent_user->first_name
            . " " . $respondent->respondent_user->last_name,
            'res_emp_no' => $respondent->respondent_user->employee_no,
            'res_position' => $respondent->respondent_user->designation->name,
            'res_department' => $respondent->respondent_user->department->name,
            'hrbp_full_name' => $respondent->incident_report->hrbp_user->first_name
            . " " . $respondent->incident_report->hrbp_user->last_name,
            'hrbp_emp_no' => $respondent->incident_report->hrbp_user->employee_no,
            'hrbp_position' => $respondent->incident_report->hrbp_user->designation->name,
            'hrm_name' => $HrManager->first_name." ".$HrManager->last_name,
            'hrm_emp_no' => $HrManager->employee_no,
        ];
        if (strpos(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            strpos(strtolower(auth()->user()->office_location->name), 'bgc') !== false
            ) {
            $data['work_location'] = "BGC";
        } elseif (strpos(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $data['work_location'] = "MARKET";
        } elseif (strpos(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            strpos(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $data['work_location'] = "BACOLOD";
        } elseif (strpos(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $data['work_location'] = "ALABANG";
        }

        $pdf = PDF::loadView('pdf.download.hr_intervention', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->download('HR Intervention documentation.pdf');
    }

    public function removeAttachment(Request $request, int $id)
    {
        $file_attach = Attachment::where('id', $id);
        $attachment = Attachment::find($id);
        $complainant = Complainant::with('respondent')->find($attachment->complainant_id);
        if (Str::contains($request->attachment, '[')) {
            $file_name = explode("[", $request->attachment);
            $file = str_replace($file_name[1].",", "", $attachment->attachments);
            $file_attach->update([
                'attachments' => $file
            ]);
            $return_attach = Attachment::find($id);
            $complainant_edit = new ComplainantEdit;
            $complainant_edit->ir_number = $complainant->respondent[0]->ir_number;
            $complainant_edit->column_edit = 'Attachment';
            $complainant_edit->old_value = $attachment->attachments;
            $complainant_edit->new_value = $file_attach->attachments;
            $complainant_edit->approver_id = (int)$request->approver_id;
            $complainant_edit->complainant_user_id = (int)$complainant->complainant_user_id;
            $complainant_edit->save();
        } elseif (Str::contains($request->attachment, ']')) {
            $file_name = explode("]", $request->attachment);
            $file = str_replace(",".$file_name[0], "", $attachment->attachments);
            $file_attach->update([
                'attachments' => $file
            ]);
            $return_attach = Attachment::find($id);
            $complainant_edit = new ComplainantEdit;
            $complainant_edit->ir_number = $complainant->respondent[0]->ir_number;
            $complainant_edit->column_edit = 'Attachment';
            $complainant_edit->old_value = $attachment->attachments;
            $complainant_edit->new_value = $return_attach->attachments;
            $complainant_edit->approver_id = (int)$request->approver_id;
            $complainant_edit->complainant_user_id = (int)$complainant->complainant_user_id;
            $complainant_edit->save();
        } else {
            $file = str_replace($request->attachment.",", "", $attachment->attachments);
            $file_attach->update([
                'attachments' => $file
            ]);
            $return_attach = Attachment::find($id);
            $complainant_edit = new ComplainantEdit;
            $complainant_edit->ir_number = $complainant->respondent[0]->ir_number;
            $complainant_edit->column_edit = 'Attachment';
            $complainant_edit->old_value = $attachment->attachments;
            $complainant_edit->new_value = $file_attach->attachments;
            $complainant_edit->approver_id = (int)$request->approver_id;
            $complainant_edit->complainant_user_id = (int)$complainant->complainant_user_id;
            $complainant_edit->save();
        }

        return response($file_attach);
    }

    public function requestEdit(Request $request)
    {
        $complainantEditRequest = new ComplainantEditRequest;
        $complainantEditRequest->ir_id = $request->ir_id;
        $complainantEditRequest->respondent_id = $request->respondent_id;
        $complainantEditRequest->ir_number = $request->ir_number;
        $complainantEditRequest->request_status = "Pending";
        $complainantEditRequest->requestor_id = $request->requestor_id;
        $complainantEditRequest->approver_id = $request->approver_id;
        $complainantEditRequest->reason = $request->reason;
        $complainantEditRequest->save();

        return $complainantEditRequest;
    }

    public function onholdRequest(Request $request)
    {
        $onholdRequest = new OnHoldRequest;
        $onholdRequest->ir_id = $request->ir_id;
        $onholdRequest->respondent_id = $request->respondent_id;
        $onholdRequest->ir_number = $request->ir_number;
        $onholdRequest->request_status = "Pending";
        $onholdRequest->requestor_id = $request->requestor_id;
        $onholdRequest->approver_id = $request->approver_id;
        $onholdRequest->reason = $request->reason;
        $onholdRequest->save();

        return $onholdRequest;
    }

    public function approveEdit(Request $request, int $id)
    {
        $requestEdit = ComplainantEditRequest::where('id', $request->id)
        ->update([
            'request_status' => 'Approved',
            // 'approver_id' => $request->approver_id
        ]);

        return $requestEdit;
    }

    public function approveOnhold(Request $request, int $id)
    {
        $requestEdit = OnHoldRequest::where('id', $request->id)
        ->update([
            'request_status' => 'Approved',
            'approver_id' => Auth()->id()
        ]);

        $respondent = Respondent::where('ir_id', $request->ir_id)->update(['status_id' => 3]);

        return $requestEdit;
    }

    public function countOnHoldRequest()
    {
        $onHoldRequest = OnHoldRequest::where('request_status', 'Pending')->get()->count();

        return $onHoldRequest;
    }

    public function irEdit(Request $request, $id)
    {
        $complainant = Complainant::where('id', $request->complainant_id)->with([
            'offense.gravity', 'respondent',
            'offense.category', 'witness_user', 'attachments', 'attendancepointssystem'
        ])->first();
        $complainant_update = Complainant::where('id', $request->complainant_id);
        $change_data = [];
        $category = Category::where('name', $request->category)->first();

        if ($request->offense != $complainant->offense->offense) {
            if ($category->id != 15) {
                $offense = Offense::where('category_id', $category->id)
                ->where('offense', $request->offense)
                ->first();

                $change_data['offense_id'] = $offense->id;
                $this->saveEditRequestChange([
                    'ir_number' => $complainant->respondent[0]->ir_number,
                    'old_value' => $complainant->offense_id,
                    'new_value' => $offense->id,
                    'approver_id' => $request->approver_id,
                    'complainant_user_id' => $complainant->complainant_user_id,
                ], 'Offense');
            } else {
                $offense = Offense::where('category_id', $category->id)
                ->where('offense', $request->offense)
                ->first();

                $change_data['attendancepointssystem_id'] = $offense->id;
                $this->saveEditRequestChange([
                    'ir_number' => $complainant->respondent[0]->ir_number,
                    'old_value' => $complainant->attendancepointssystem_id,
                    'new_value' => $offense->id,
                    'approver_id' => $request->approver_id,
                    'complainant_user_id' => $complainant->complainant_user_id,
                ], 'Offense');
            }
        }
        if ($request->date_incident_start != $complainant->date_incident_start) {
            $change_data['date_incident_start'] = $request->date_incident_start;
            $this->saveEditRequestChange([
                'ir_number' => $complainant->respondent[0]->ir_number,
                'old_value' => $complainant->date_incident_start,
                'new_value' => $request->date_incident_start,
                'approver_id' => $request->approver_id,
                'complainant_user_id' => $complainant->complainant_user_id,
            ], 'Date Incident Start');
        }
        if ($request->date_incident_end != $complainant->date_incident_end) {
            $change_data['date_incident_end'] = $request->date_incident_end;
            $this->saveEditRequestChange([
                'ir_number' => $complainant->respondent[0]->ir_number,
                'old_value' => $complainant->date_incident_end,
                'new_value' => $request->date_incident_start,
                'approver_id' => $request->approver_id,
                'complainant_user_id' => $complainant->complainant_user_id,
            ], 'Date Incident End');
        }
        if ($request->description != $complainant->description) {
            $change_data['description'] = $request->description;
            $this->saveEditRequestChange([
                'ir_number' => $complainant->respondent[0]->ir_number,
                'old_value' => $complainant->description,
                'new_value' => $request->description,
                'approver_id' => $request->approver_id,
                'complainant_user_id' => $complainant->complainant_user_id,
            ], 'Description');
        }
        if ($complainant->attachment_type == 1) {
            $complainant->attachment_type = true;
        } else {
            $complainant->attachment_type = false;
        }
        if ($request->attachment_type != $complainant->attachment_type &&
            ($request->attachment_type == 1 || $request->attachment_type == 0)) {
            $change_data['attachment_type'] = 0;

            if ($request->attachment_type) {
                $change_data['attachment_type'] = 1;
            }
            $this->saveEditRequestChange([
                'ir_number' => $complainant->respondent[0]->ir_number,
                'old_value' => $complainant->description,
                'new_value' => $request->attachment_type,
                'approver_id' => $change_data['attachment_type'],
                'complainant_user_id' => $complainant->complainant_user_id,
            ], 'is TK image attachment');
        }
        $respondent = Respondent::where('ir_id', $request->ir_id)
            ->where('complainant_id', $complainant->id)
            ->first();
        $attachment = Attachment::where('respondent_id', $respondent->id)
            ->where('complainant_id', $complainant->id)
            ->first();
        if (!empty($change_data)) {
            $complainant = Complainant::where('id', $request->complainant_id)->update($change_data);
        }

        foreach ($request->attachments as $value) {
            if ($value) {
                if (strcmp($attachment->attachments, $value !== 0)) {
                    $attach = explode(']', $attachment->attachments);
                    $change_attach = '';
                    $explode_attach = explode('/', $value);
                    foreach ($explode_attach as $val) {
                        if ($val != 'complainants') {
                            $change_attach = $change_attach ."\/".$val;
                        } else {
                            $change_attach = $change_attach.$val;
                        }
                    }
                    $final_attach = $attach[0] . "," . '"'. $change_attach . '"]';
                    $attachment = Attachment::where('respondent_id', $respondent->id)
                        ->where('complainant_id', $complainant->id)
                        ->update([
                            'attachments' => $final_attach
                        ]);
                    $change_data['attachment'] = $final_attach;
                    $this->saveEditRequestChange([
                        'ir_number' => $complainant->respondent[0]->ir_number,
                        'old_value' => $attachment->attachments,
                        'new_value' => $change_data['attachment'],
                        'approver_id' => $change_data['attachment_type'],
                        'complainant_user_id' => $complainant->complainant_user_id,
                    ], 'is TK image attachment');
                }
            }
        }
        if (!empty($change_data)) {
            $request_edit = ComplainantEditRequest::where('id', $id)->update([
                'request_status' => 'Done'
            ]);
        }
    }

    private function saveEditRequestChange(array $change_array, string $column)
    {
        $change = new ComplainantEdit;
        $change->ir_number = $change_array['ir_number'];
        $change->column_edit = $column;
        $change->old_value = $change_array['old_value'];
        $change->new_value = $change_array['new_value'];
        $change->approver_id = (int)$change_array['approver_id'];
        $change->complainant_user_id = (int)$change_array['complainant_user_id'];
        $change->save();

        return $change;
    }

    public function irEditAttachFile(Request $request)
    {
        if ($request->file('pics')) {
            $storage = 'public/attachments/complainants/'
                        . auth()->user()->employee_no
                        . '/' . Carbon::today()->format('Y-m-d')
                        . '/';

            $uploaded = [];
            foreach ($request->file('pics') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'complainants/'
                            . auth()->user()->employee_no . '/'
                            . Carbon::today()->format('Y-m-d')
                            . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            return response(['uploaded' => $uploaded ]);
        }
    }

    public function article297Terminable($incident_report_id)
    {
        $art297 = Article297Terminable::where('incident_report_id', $incident_report_id)->first();

        return json_decode($art297->article_297_edited, true);
    }
}
