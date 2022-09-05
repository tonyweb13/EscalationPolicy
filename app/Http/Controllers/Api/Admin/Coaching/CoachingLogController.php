<?php

namespace App\Http\Controllers\Api\Admin\Coaching;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use App\Models\Settings\Office;
use App\Http\Controllers\Api\Admin\Settings\Coc\OffenseController;
use App\Http\Controllers\Api\ValidatorController;
use Illuminate\Support\Collection;
use App\Models\{User, Department};
use App\Models\Admin\Settings\Coc\Offense;
use App\Models\Admin\Coaching\{
    DashboardRaw,
    DisputeDashboard,
    EfficiencyGoal,
    Portfolio,
    MonthlyPerformance,
    CoachingLog,
    ImageAttachment,
    KeyPerformanceIndicator
};
use App\Models\Admin\Coaching\KPI\{
    AttendanceReliabilityKpi,
    CoachingLogKpi,
    LoanAmountKpi,
    QaComplianceKpi,
    QaScoreKpi,
    CorrectionKpi,
    KnowledgeKpi,
    ScoreCardKpi
};
use App\Jobs\{
    EmailCoachingLog,
};
use App\Models\Admin\Coaching\WeeklyPerformance\{
    WeeklyPerformances,
    WeekOnePerformances,
    WeekTwoPerformances,
    WeekThreePerformances,
    WeekFourPerformances,
    WeekFivePerformances,
    WeightPerformances,
    CurrentScorePerformances,
    RatingRightPerformances,
    RatingLeftPerformances
};
use App\Models\Admin\Coaching\NewForms\{
    AdminForms,
    HrisForms,
    CNBForms,
    FinalPatForms,
    ManagerRatingSiteLeadForms,
    OnboardingForms,
    PayrollForms,
    RecruitmentForms,
    SelfRatingHrbpForms,
    SelfRatingHrbpSiteLeadForms,
    SourcingForms,
    SupervisorHrbpForms,
    SupervisorRecruitmentForms,
    FinalPayForms
};
use DB;
use PDF;
use Excel;
use Exception;
use Carbon\Carbon;

class CoachingLogController extends Controller
{
    public function selectedUser($employee_no)
    {
        $user = User::where('employee_no', $employee_no)->first();

        return response()->json($user);
    }

    public function userToWeek($employee_no, $month): JsonResponse
    {
        $dashboard_raw = DashboardRaw::where('employee_no', $employee_no)
        ->whereMonth('report_date', $month)
        ->select('week_number')
        ->orderBy('week_number', 'asc')
        ->get()
        ->groupBy('week_number');
        $data = [];
        foreach ($dashboard_raw as $key => $value) {
            array_push($data, $key);
        }

        return response()->json($data);
    }

    public function dropdownDepartment(): JsonResponse
    {
        $department = Department::select('id as value', DB::raw('name AS text'))
            ->orderByRaw('name')
            ->get();

        return response()->json($department);
    }

    public function dropdownDeptId($department_id): JsonResponse
    {
        $user = User::where('department_id', $department_id)
            ->select('id as value', DB::raw('CONCAT(TRIM(last_name), " ", first_name) AS text'))
            ->orderByRaw('TRIM(last_name)')
            ->get();

        $employee_no = User::where('department_id', $department_id)
            ->select('id as value', DB::raw('CONVERT(employee_no, CHAR) AS text'))
            ->orderByRaw('employee_no')
            ->get();

        return response()->json(['user' => $user, 'emp_no' => $employee_no]);
    }

    public function dropdownEmployeeName($employee_name): JsonResponse
    {
        $name = explode(' ', $employee_name);
        $employee_no = User::where('last_name', $name[0])
            ->where('first_name', $name[1])
            ->select('id as value', DB::raw('CONVERT(employee_no, CHAR) AS text'))
            ->orderByRaw('employee_no')
            ->get();

        return response()->json($employee_no);
    }

    public function monthly(): JsonResponse
    {
        $monthly = MonthlyPerformance::where('monthly', 1)->get();

        return response()->json($monthly);
    }

    public function weekly(): JsonResponse
    {
        $weekly = MonthlyPerformance::where('weekly', 1)->get();

        return response()->json($weekly);
    }

    public function disputeDashboard(): JsonResponse
    {
        $dispute_all = [];
        $dispute = DisputeDashboard::all();

        foreach ($dispute as $key => $value) {
            $user_info = User::where('employee_no', $value->employee_no)->first();

            $data = [
                'created_by' => $value->created_by,
                'changes' => $user_info->first_name." ".$user_info->last_name." - ".$user_info->employee_no,
                'date_updated' => $value->date_updated
            ];
            array_push($dispute_all, $data);
        }

        return response()->json($dispute_all);
    }

    public function dropdownEmployeeNo(): JsonResponse
    {
        $employee_no = User::select('id as value', DB::raw('CONVERT(employee_no, CHAR) AS text'))
            ->orderByRaw('employee_no')
            ->get();

        return response()->json($employee_no);
    }

    public function dropdownProfileName(): JsonResponse
    {
        $portfolio = Portfolio::select('id as value', DB::raw('CONCAT(description) AS text'))->get();

        return response()->json($portfolio);
    }

    public function createEfficiencyGoal(Request $request)
    {
        $port_name_extract_1 = explode('-', $request->params['profile_name']['text']);
        $portfolio_name_extract_2 = explode(' ', $port_name_extract_1[0]);
        $filter_port_name = array_filter($portfolio_name_extract_2);
        $portfolio_name = implode(' ', $filter_port_name);
        $portfolio = EfficiencyGoal::create([
            'portfolio_name' => $portfolio_name,
            'portfolio_id' => $request->params['profile_name']['value'],
            'priority' => $request->params['priority'],
            'in_call' => $request->params['in_call'],
            'ready' => $request->params['ready'],
            'wrap_up' => $request->params['wrap_up'],
            'not_ready' => $request->params['not_ready']
        ]);

        return response()->json();
    }

    public function updateEfficiencyGoal(int $id, Request $request)
    {
        $port_name_extract_1 = explode('-', $request->params['profile_name']['text']);
        $portfolio_name_extract_2 = explode(' ', $port_name_extract_1[0]);
        $filter_port_name = array_filter($portfolio_name_extract_2);
        $portfolio_name = implode(' ', $filter_port_name);
        $portfolio = EfficiencyGoal::updateOrCreate(['id' => $id], [
            'portfolio_name' => $portfolio_name,
            'portfolio_id' => $request->params['profile_name']['value'],
            'priority' => $request->params['priority'],
            'in_call' => $request->params['in_call'],
            'ready' => $request->params['ready'],
            'wrap_up' => $request->params['wrap_up'],
            'not_ready' => $request->params['not_ready']
        ]);

        return response()->json($portfolio);
    }

    public function deleteEfficiencyGoal(int $id)
    {
        $portfolio = EfficiencyGoal::findOrFail($id)->delete();

        return response()->json($portfolio);
    }

    public function invalid($id)
    {
        $coaching = CoachingLog::where('id', $id)->update([
            'status' => 1,
        ]);

        return response()->json($coaching);
    }

    public function createWeekly(Request $request)
    {
        foreach (json_decode($request->week) as $key => $value) {
            if ($value->week1) {
                $week_one = WeekOnePerformances::create([
                    'target_in_call' => $value->week1->target->in_call,
                    'target_ready' => $value->week1->target->ready,
                    'target_wrap_up' => $value->week1->target->wrap_up,
                    'target_not_ready' => $value->week1->target->not_ready,
                    'target_loan_origination' => $value->week1->target->loan_origination,
                    'target_qa_scores' => $value->week1->target->qa_scores,
                    'target_compliance' => $value->week1->target->compliance,
                    'target_attendance' => $value->week1->target->attendance,
                    'actual_in_call' => $value->week1->actual->in_call,
                    'actual_ready' => $value->week1->actual->ready,
                    'actual_wrap_up' => $value->week1->actual->wrap_up,
                    'actual_not_ready' => $value->week1->actual->not_ready,
                    'actual_loan_origination' => $value->week1->actual->loan_origination,
                    'actual_qa_scores' => $value->week1->actual->qa_scores,
                    'actual_compliance' => $value->week1->actual->compliance,
                    'actual_attendance' => $value->week1->actual->attendance,
                ]);
            }
            if ($value->week2) {
                $week_two = WeekTwoPerformances::create([
                    'target_in_call' => $value->week2->target->in_call,
                    'target_ready' => $value->week2->target->ready,
                    'target_wrap_up' => $value->week2->target->wrap_up,
                    'target_not_ready' => $value->week2->target->not_ready,
                    'target_loan_origination' => $value->week2->target->loan_origination,
                    'target_qa_scores' => $value->week2->target->qa_scores,
                    'target_compliance' => $value->week2->target->compliance,
                    'target_attendance' => $value->week2->target->attendance,
                    'actual_in_call' => $value->week2->actual->in_call,
                    'actual_ready' => $value->week2->actual->ready,
                    'actual_wrap_up' => $value->week2->actual->wrap_up,
                    'actual_not_ready' => $value->week2->actual->not_ready,
                    'actual_loan_origination' => $value->week2->actual->loan_origination,
                    'actual_qa_scores' => $value->week2->actual->qa_scores,
                    'actual_compliance' => $value->week2->actual->compliance,
                    'actual_attendance' => $value->week2->actual->attendance,
                ]);
            }
            if ($value->week3) {
                $week_three = WeekThreePerformances::create([
                    'target_in_call' => $value->week3->target->in_call,
                    'target_ready' => $value->week3->target->ready,
                    'target_wrap_up' => $value->week3->target->wrap_up,
                    'target_not_ready' => $value->week3->target->not_ready,
                    'target_loan_origination' => $value->week3->target->loan_origination,
                    'target_qa_scores' => $value->week3->target->qa_scores,
                    'target_compliance' => $value->week3->target->compliance,
                    'target_attendance' => $value->week3->target->attendance,
                    'actual_in_call' => $value->week3->actual->in_call,
                    'actual_ready' => $value->week3->actual->ready,
                    'actual_wrap_up' => $value->week3->actual->wrap_up,
                    'actual_not_ready' => $value->week3->actual->not_ready,
                    'actual_loan_origination' => $value->week3->actual->loan_origination,
                    'actual_qa_scores' => $value->week3->actual->qa_scores,
                    'actual_compliance' => $value->week3->actual->compliance,
                    'actual_attendance' => $value->week3->actual->attendance,
                ]);
            }
            if ($value->week4) {
                $week_four = WeekFourPerformances::create([
                    'target_in_call' => $value->week4->target->in_call,
                    'target_ready' => $value->week4->target->ready,
                    'target_wrap_up' => $value->week4->target->wrap_up,
                    'target_not_ready' => $value->week4->target->not_ready,
                    'target_loan_origination' => $value->week4->target->loan_origination,
                    'target_qa_scores' => $value->week4->target->qa_scores,
                    'target_compliance' => $value->week4->target->compliance,
                    'target_attendance' => $value->week4->target->attendance,
                    'actual_in_call' => $value->week4->actual->in_call,
                    'actual_ready' => $value->week4->actual->ready,
                    'actual_wrap_up' => $value->week4->actual->wrap_up,
                    'actual_not_ready' => $value->week4->actual->not_ready,
                    'actual_loan_origination' => $value->week4->actual->loan_origination,
                    'actual_qa_scores' => $value->week4->actual->qa_scores,
                    'actual_compliance' => $value->week4->actual->compliance,
                    'actual_attendance' => $value->week4->actual->attendance,
                ]);
            }
            if ($value->week5) {
                $week_five = WeekFivePerformances::create([
                    'target_in_call' => $value->week5->target->in_call,
                    'target_ready' => $value->week5->target->ready,
                    'target_wrap_up' => $value->week5->target->wrap_up,
                    'target_not_ready' => $value->week5->target->not_ready,
                    'target_loan_origination' => $value->week5->target->loan_origination,
                    'target_qa_scores' => $value->week5->target->qa_scores,
                    'target_compliance' => $value->week5->target->compliance,
                    'target_attendance' => $value->week5->target->attendance,
                    'actual_in_call' => $value->week5->actual->in_call,
                    'actual_ready' => $value->week5->actual->ready,
                    'actual_wrap_up' => $value->week5->actual->wrap_up,
                    'actual_not_ready' => $value->week5->actual->not_ready,
                    'actual_loan_origination' => $value->week5->actual->loan_origination,
                    'actual_qa_scores' => $value->week5->actual->qa_scores,
                    'actual_compliance' => $value->week5->actual->compliance,
                    'actual_attendance' => $value->week5->actual->attendance,
                ]);
            }
        }
        $weight = WeightPerformances::create([
            'loan_amount' => $request->weight_loan_amount,
            'qa_score' => $request->weight_qa_score,
            'compliance' => $request->weight_compliance,
            'correction' => $request->weight_correction,
            'attendance_reliability' => $request->weight_attendance_reliability,
            'total' => $request->weight_total,
        ]);
        $current_score = CurrentScorePerformances::create([
            'loan_amount' => $request->current_score_loan_amount,
            'qa_score' => $request->current_score_qa_score,
            'compliance' => $request->current_score_compliance,
            'correction' => $request->current_score_correction,
            'attendance_reliability' => $request->current_score_attendance_reliability,
        ]);
        $rating_right = RatingRightPerformances::create([
            'loan_amount' => $request->rating_right_side_loan_amount,
            'qa_score' => $request->rating_right_side_qa_score,
            'compliance' => $request->rating_right_side_compliance,
            'correction' => $request->rating_right_side_correction,
            'attendance_reliability' => $request->rating_right_side_attendance_reliability,
            'total' => $request->rating_right_side_total,
        ]);
        $rating_left = RatingLeftPerformances::create([
            'loan_amount' => $request->rating_left_side_loan_amount,
            'qa_score' => $request->rating_left_side_qa_score,
            'compliance' => $request->rating_left_side_compliance,
            'correction' => $request->rating_left_side_correction,
            'attendance_reliability' => $request->rating_left_side_attendance_reliability,
            'total' => $request->rating_left_side_total,
        ]);
        $weekly_performance = WeeklyPerformances::create([
            'employee_no' => $request->employee_no,
            'identified_behavior' => $request->identified_behavior,
            'root_cause' =>  $request->root_cause,
            'action_plans' =>  $request->action_plans,
            'base_performance' =>  $request->base_performance,
            'goal' =>  $request->goal,
            'extended_action_plan' =>  $request->extended_action_plan,
            'extended_action_plan' =>  $request->extended_action_plan,
            'justification' =>  $request->justification,
            'additional_feedback' =>  $request->additional_feedback,
            'week1_id' => $week_one->id ? $week_one->id : '',
            'week2_id' => $week_two->id ? $week_two->id : '',
            'week3_id' => $week_three->id ? $week_three->id : '',
            'week4_id' => $week_four->id ? $week_four->id : '',
            'week5_id' => $week_five->id ? $week_five->id : '',
            'weight_id' => $weight->id,
            'current_score_id' => $current_score->id,
            'rating_left_side_id' => $rating_right->id,
            'rating_right_side_id' => $rating_left->id,
        ]);

        $coaching_log_count = CoachingLog::count();
        $cl_number_initial = STR_PAD($coaching_log_count+1, 9, '0', STR_PAD_LEFT);
        $coaching = new CoachingLog;
        $coaching->id = $coaching_log_count+1;
        $coaching->employee_no = $request->employee_no;
        $coaching->cl_number = $cl_number_initial;
        $coaching->performance_id = $weekly_performance->id;
        $coaching->created_by = auth()->user()->first_name . " " . auth()->user()->last_name;
        $coaching->created_by_employee_id = auth()->user()->id;
        $coaching->date_created = date('Y-m-d h:m:s');
        $coaching->save();
        $user = User::where('employee_no', $request->employee_no)->first();
        if (strlen($user->email_address) > 0) {
            EmailCoachingLog::dispatch(
                $coaching->cl_number,
                $user->email_address,
                $coaching->created_at,
                'coaching_response'
            );

            return response()->json($coaching);
        } else {

            return response()->json($coaching);
        }
    }

    public function addWeekly(Request $request)
    {
        foreach (json_decode($request->week) as $key => $value) {
            if ($value->week1) {
                $week_one = WeekOnePerformances::create([
                    'target_in_call' => $value->week1->target->in_call,
                    'target_ready' => $value->week1->target->ready,
                    'target_wrap_up' => $value->week1->target->wrap_up,
                    'target_not_ready' => $value->week1->target->not_ready,
                    'target_loan_origination' => $value->week1->target->loan_origination,
                    'target_qa_scores' => $value->week1->target->qa_scores,
                    'target_compliance' => $value->week1->target->compliance,
                    'target_attendance' => $value->week1->target->attendance,
                    'actual_in_call' => $value->week1->actual->in_call,
                    'actual_ready' => $value->week1->actual->ready,
                    'actual_wrap_up' => $value->week1->actual->wrap_up,
                    'actual_not_ready' => $value->week1->actual->not_ready,
                    'actual_loan_origination' => $value->week1->actual->loan_origination,
                    'actual_qa_scores' => $value->week1->actual->qa_scores,
                    'actual_compliance' => $value->week1->actual->compliance,
                    'actual_attendance' => $value->week1->actual->attendance,
                ]);
            }
            if ($value->week2) {
                $week_two = WeekTwoPerformances::create([
                    'target_in_call' => $value->week2->target->in_call,
                    'target_ready' => $value->week2->target->ready,
                    'target_wrap_up' => $value->week2->target->wrap_up,
                    'target_not_ready' => $value->week2->target->not_ready,
                    'target_loan_origination' => $value->week2->target->loan_origination,
                    'target_qa_scores' => $value->week2->target->qa_scores,
                    'target_compliance' => $value->week2->target->compliance,
                    'target_attendance' => $value->week2->target->attendance,
                    'actual_in_call' => $value->week2->actual->in_call,
                    'actual_ready' => $value->week2->actual->ready,
                    'actual_wrap_up' => $value->week2->actual->wrap_up,
                    'actual_not_ready' => $value->week2->actual->not_ready,
                    'actual_loan_origination' => $value->week2->actual->loan_origination,
                    'actual_qa_scores' => $value->week2->actual->qa_scores,
                    'actual_compliance' => $value->week2->actual->compliance,
                    'actual_attendance' => $value->week2->actual->attendance,
                ]);
            }
            if ($value->week3) {
                $week_three = WeekThreePerformances::create([
                    'target_in_call' => $value->week3->target->in_call,
                    'target_ready' => $value->week3->target->ready,
                    'target_wrap_up' => $value->week3->target->wrap_up,
                    'target_not_ready' => $value->week3->target->not_ready,
                    'target_loan_origination' => $value->week3->target->loan_origination,
                    'target_qa_scores' => $value->week3->target->qa_scores,
                    'target_compliance' => $value->week3->target->compliance,
                    'target_attendance' => $value->week3->target->attendance,
                    'actual_in_call' => $value->week3->actual->in_call,
                    'actual_ready' => $value->week3->actual->ready,
                    'actual_wrap_up' => $value->week3->actual->wrap_up,
                    'actual_not_ready' => $value->week3->actual->not_ready,
                    'actual_loan_origination' => $value->week3->actual->loan_origination,
                    'actual_qa_scores' => $value->week3->actual->qa_scores,
                    'actual_compliance' => $value->week3->actual->compliance,
                    'actual_attendance' => $value->week3->actual->attendance,
                ]);
            }
            if ($value->week4) {
                $week_four = WeekFourPerformances::create([
                    'target_in_call' => $value->week4->target->in_call,
                    'target_ready' => $value->week4->target->ready,
                    'target_wrap_up' => $value->week4->target->wrap_up,
                    'target_not_ready' => $value->week4->target->not_ready,
                    'target_loan_origination' => $value->week4->target->loan_origination,
                    'target_qa_scores' => $value->week4->target->qa_scores,
                    'target_compliance' => $value->week4->target->compliance,
                    'target_attendance' => $value->week4->target->attendance,
                    'actual_in_call' => $value->week4->actual->in_call,
                    'actual_ready' => $value->week4->actual->ready,
                    'actual_wrap_up' => $value->week4->actual->wrap_up,
                    'actual_not_ready' => $value->week4->actual->not_ready,
                    'actual_loan_origination' => $value->week4->actual->loan_origination,
                    'actual_qa_scores' => $value->week4->actual->qa_scores,
                    'actual_compliance' => $value->week4->actual->compliance,
                    'actual_attendance' => $value->week4->actual->attendance,
                ]);
            }
            if ($value->week5) {
                $week_five = WeekFivePerformances::create([
                    'target_in_call' => $value->week5->target->in_call,
                    'target_ready' => $value->week5->target->ready,
                    'target_wrap_up' => $value->week5->target->wrap_up,
                    'target_not_ready' => $value->week5->target->not_ready,
                    'target_loan_origination' => $value->week5->target->loan_origination,
                    'target_qa_scores' => $value->week5->target->qa_scores,
                    'target_compliance' => $value->week5->target->compliance,
                    'target_attendance' => $value->week5->target->attendance,
                    'actual_in_call' => $value->week5->actual->in_call,
                    'actual_ready' => $value->week5->actual->ready,
                    'actual_wrap_up' => $value->week5->actual->wrap_up,
                    'actual_not_ready' => $value->week5->actual->not_ready,
                    'actual_loan_origination' => $value->week5->actual->loan_origination,
                    'actual_qa_scores' => $value->week5->actual->qa_scores,
                    'actual_compliance' => $value->week5->actual->compliance,
                    'actual_attendance' => $value->week5->actual->attendance,
                ]);
            }
        }
        $weight = WeightPerformances::create([
            'loan_amount' => $request->weight_loan_amount,
            'qa_score' => $request->weight_qa_score,
            'compliance' => $request->weight_compliance,
            'correction' => $request->weight_correction,
            'attendance_reliability' => $request->weight_attendance_reliability,
            'total' => $request->weight_total,
        ]);
        $current_score = CurrentScorePerformances::create([
            'loan_amount' => $request->current_score_loan_amount,
            'qa_score' => $request->current_score_qa_score,
            'compliance' => $request->current_score_compliance,
            'correction' => $request->current_score_correction,
            'attendance_reliability' => $request->current_score_attendance_reliability,
        ]);
        $rating_right = RatingRightPerformances::create([
            'loan_amount' => $request->rating_right_side_loan_amount,
            'qa_score' => $request->rating_right_side_qa_score,
            'compliance' => $request->rating_right_side_compliance,
            'correction' => $request->rating_right_side_correction,
            'attendance_reliability' => $request->rating_right_side_attendance_reliability,
            'total' => $request->rating_right_side_total,
        ]);
        $rating_left = RatingLeftPerformances::create([
            'loan_amount' => $request->rating_left_side_loan_amount,
            'qa_score' => $request->rating_left_side_qa_score,
            'compliance' => $request->rating_left_side_compliance,
            'correction' => $request->rating_left_side_correction,
            'attendance_reliability' => $request->rating_left_side_attendance_reliability,
            'total' => $request->rating_left_side_total,
        ]);
        $weekly_performance = WeeklyPerformances::create([
            'employee_no' => $request->employee_no,
            'identified_behavior' => $request->identified_behavior,
            'root_cause' =>  $request->root_cause,
            'action_plans' =>  $request->action_plans,
            'base_performance' =>  $request->base_performance,
            'goal' =>  $request->goal,
            'extended_action_plan' =>  $request->extended_action_plan,
            'extended_action_plan' =>  $request->extended_action_plan,
            'justification' =>  $request->justification,
            'additional_feedback' =>  $request->additional_feedback,
            'week1_id' => $week_one->id ? $week_one->id : '',
            'week2_id' => $week_two->id ? $week_two->id : '',
            'week3_id' => $week_three->id ? $week_three->id : '',
            'week4_id' => $week_four->id ? $week_four->id : '',
            'week5_id' => $week_five->id ? $week_five->id : '',
            'weight_id' => $weight->id,
            'current_score_id' => $current_score->id,
            'rating_left_side_id' => $rating_right->id,
            'rating_right_side_id' => $rating_left->id,
        ]);

        $coaching_log_count = CoachingLog::count();
        $cl_number_initial = STR_PAD($coaching_log_count+1, 9, '0', STR_PAD_LEFT);
        $coaching = new CoachingLog;
        $coaching->id = $coaching_log_count+1;
        $coaching->employee_no = $request->employee_no;
        $coaching->cl_number = $cl_number_initial;
        $coaching->performance_id = $weekly_performance->id;
        $coaching->created_by = auth()->user()->first_name . " " . auth()->user()->last_name;
        $coaching->created_by_employee_id = auth()->user()->id;
        $coaching->date_created = date('Y-m-d h:m:s');
        $coaching->save();
        $user = User::where('employee_no', $request->employee_no)->first();
        $coaching_grayeded_out = WeeklyPerformances::where('id', $request->performance_id)
        ->update([
            'added_week' => 1
        ]);

        if (strlen($user->email_address) > 0) {
            EmailCoachingLog::dispatch(
                $coaching->cl_number,
                $user->email_address,
                $coaching->created_at,
                'coaching_response'
            );

            return response()->json($coaching);
        } else {

            return response()->json($coaching);
        }
    }

    public function editWeekly(Request $request)
    {
        foreach (json_decode($request->week) as $key => $value) {
            if ($value->week1) {
                $week_one = WeekOnePerformances::updateOrCreate(['id' => $request->week1_id], [
                    'target_in_call' => $value->week1->target->in_call,
                    'target_ready' => $value->week1->target->ready,
                    'target_wrap_up' => $value->week1->target->wrap_up,
                    'target_not_ready' => $value->week1->target->not_ready,
                    'target_loan_origination' => $value->week1->target->loan_origination,
                    'target_qa_scores' => $value->week1->target->qa_scores,
                    'target_compliance' => $value->week1->target->compliance,
                    'target_attendance' => $value->week1->target->attendance,
                    'actual_in_call' => $value->week1->actual->in_call,
                    'actual_ready' => $value->week1->actual->ready,
                    'actual_wrap_up' => $value->week1->actual->wrap_up,
                    'actual_not_ready' => $value->week1->actual->not_ready,
                    'actual_loan_origination' => $value->week1->actual->loan_origination,
                    'actual_qa_scores' => $value->week1->actual->qa_scores,
                    'actual_compliance' => $value->week1->actual->compliance,
                    'actual_attendance' => $value->week1->actual->attendance,
                ]);
            }
            if ($value->week2) {
                $week_two = WeekTwoPerformances::updateOrCreate(['id' => $request->week2_id], [
                    'target_in_call' => $value->week2->target->in_call,
                    'target_ready' => $value->week2->target->ready,
                    'target_wrap_up' => $value->week2->target->wrap_up,
                    'target_not_ready' => $value->week2->target->not_ready,
                    'target_loan_origination' => $value->week2->target->loan_origination,
                    'target_qa_scores' => $value->week2->target->qa_scores,
                    'target_compliance' => $value->week2->target->compliance,
                    'target_attendance' => $value->week2->target->attendance,
                    'actual_in_call' => $value->week2->actual->in_call,
                    'actual_ready' => $value->week2->actual->ready,
                    'actual_wrap_up' => $value->week2->actual->wrap_up,
                    'actual_not_ready' => $value->week2->actual->not_ready,
                    'actual_loan_origination' => $value->week2->actual->loan_origination,
                    'actual_qa_scores' => $value->week2->actual->qa_scores,
                    'actual_compliance' => $value->week2->actual->compliance,
                    'actual_attendance' => $value->week2->actual->attendance,
                ]);
            }
            if ($value->week3) {
                $week_three = WeekThreePerformances::updateOrCreate(['id' => $request->week3_id], [
                    'target_in_call' => $value->week3->target->in_call,
                    'target_ready' => $value->week3->target->ready,
                    'target_wrap_up' => $value->week3->target->wrap_up,
                    'target_not_ready' => $value->week3->target->not_ready,
                    'target_loan_origination' => $value->week3->target->loan_origination,
                    'target_qa_scores' => $value->week3->target->qa_scores,
                    'target_compliance' => $value->week3->target->compliance,
                    'target_attendance' => $value->week3->target->attendance,
                    'actual_in_call' => $value->week3->actual->in_call,
                    'actual_ready' => $value->week3->actual->ready,
                    'actual_wrap_up' => $value->week3->actual->wrap_up,
                    'actual_not_ready' => $value->week3->actual->not_ready,
                    'actual_loan_origination' => $value->week3->actual->loan_origination,
                    'actual_qa_scores' => $value->week3->actual->qa_scores,
                    'actual_compliance' => $value->week3->actual->compliance,
                    'actual_attendance' => $value->week3->actual->attendance,
                ]);
            }
            if ($value->week4) {
                $week_four = WeekFourPerformances::updateOrCreate(['id' => $request->week4_id], [
                    'target_in_call' => $value->week4->target->in_call,
                    'target_ready' => $value->week4->target->ready,
                    'target_wrap_up' => $value->week4->target->wrap_up,
                    'target_not_ready' => $value->week4->target->not_ready,
                    'target_loan_origination' => $value->week4->target->loan_origination,
                    'target_qa_scores' => $value->week4->target->qa_scores,
                    'target_compliance' => $value->week4->target->compliance,
                    'target_attendance' => $value->week4->target->attendance,
                    'actual_in_call' => $value->week4->actual->in_call,
                    'actual_ready' => $value->week4->actual->ready,
                    'actual_wrap_up' => $value->week4->actual->wrap_up,
                    'actual_not_ready' => $value->week4->actual->not_ready,
                    'actual_loan_origination' => $value->week4->actual->loan_origination,
                    'actual_qa_scores' => $value->week4->actual->qa_scores,
                    'actual_compliance' => $value->week4->actual->compliance,
                    'actual_attendance' => $value->week4->actual->attendance,
                ]);
            }
            if ($value->week5) {
                $week_five = WeekFivePerformances::updateOrCreate(['id' => $request->week5_id], [
                    'target_in_call' => $value->week5->target->in_call,
                    'target_ready' => $value->week5->target->ready,
                    'target_wrap_up' => $value->week5->target->wrap_up,
                    'target_not_ready' => $value->week5->target->not_ready,
                    'target_loan_origination' => $value->week5->target->loan_origination,
                    'target_qa_scores' => $value->week5->target->qa_scores,
                    'target_compliance' => $value->week5->target->compliance,
                    'target_attendance' => $value->week5->target->attendance,
                    'actual_in_call' => $value->week5->actual->in_call,
                    'actual_ready' => $value->week5->actual->ready,
                    'actual_wrap_up' => $value->week5->actual->wrap_up,
                    'actual_not_ready' => $value->week5->actual->not_ready,
                    'actual_loan_origination' => $value->week5->actual->loan_origination,
                    'actual_qa_scores' => $value->week5->actual->qa_scores,
                    'actual_compliance' => $value->week5->actual->compliance,
                    'actual_attendance' => $value->week5->actual->attendance,
                ]);
            }
        }
        $weight = WeightPerformances::updateOrCreate(['id' => $request->weight_id], [
            'loan_amount' => $request->weight_loan_amount,
            'qa_score' => $request->weight_qa_score,
            'compliance' => $request->weight_compliance,
            'correction' => $request->weight_correction,
            'attendance_reliability' => $request->weight_attendance_reliability,
            'total' => $request->weight_total,
        ]);
        $current_score = CurrentScorePerformances::updateOrCreate(['id' => $request->current_score_id], [
            'loan_amount' => $request->current_score_loan_amount,
            'qa_score' => $request->current_score_qa_score,
            'compliance' => $request->current_score_compliance,
            'correction' => $request->current_score_correction,
            'attendance_reliability' => $request->current_score_attendance_reliability,
        ]);
        $rating_right = RatingRightPerformances::updateOrCreate(['id' => $request->rating_right_id], [
            'loan_amount' => $request->rating_right_side_loan_amount,
            'qa_score' => $request->rating_right_side_qa_score,
            'compliance' => $request->rating_right_side_compliance,
            'correction' => $request->rating_right_side_correction,
            'attendance_reliability' => $request->rating_right_side_attendance_reliability,
            'total' => $request->rating_right_side_total,
        ]);
        $rating_left = RatingLeftPerformances::updateOrCreate(['id' => $request->rating_left_id], [
            'loan_amount' => $request->rating_left_side_loan_amount,
            'qa_score' => $request->rating_left_side_qa_score,
            'compliance' => $request->rating_left_side_compliance,
            'correction' => $request->rating_left_side_correction,
            'attendance_reliability' => $request->rating_left_side_attendance_reliability,
            'total' => $request->rating_left_side_total,
        ]);
        $weekly_performance = WeeklyPerformances::updateOrCreate(['id' => $request->performance_id], [
            'employee_no' => $request->employee_no,
            'identified_behavior' => $request->identified_behavior,
            'root_cause' =>  $request->root_cause,
            'action_plans' =>  $request->action_plans,
            'base_performance' =>  $request->base_performance,
            'goal' =>  $request->goal,
            'extended_action_plan' =>  $request->extended_action_plan,
            'extended_action_plan' =>  $request->extended_action_plan,
            'justification' =>  $request->justification,
            'additional_feedback' =>  $request->additional_feedback,
            'week1_id' => $week_one->id ? $week_one->id : '',
            'week2_id' => $week_two->id ? $week_two->id : '',
            'week3_id' => $week_three->id ? $week_three->id : '',
            'week4_id' => $week_four->id ? $week_four->id : '',
            'week5_id' => $week_five->id ? $week_five->id : '',
            'weight_id' => $weight->id,
            'current_score_id' => $current_score->id,
            'rating_left_side_id' => $rating_right->id,
            'rating_right_side_id' => $rating_left->id,
        ]);

        $coaching = CoachingLog::with([
            'performance.week_one',
            'performance.week_two',
            'performance.week_three',
            'performance.week_four',
            'performance.week_five',
            'performance.weight',
            'performance.current_score',
            'performance.rating_right',
            'performance.rating_left',
        ])->find($request->coaching_log_id);

        return response()->json($coaching);
    }

    public function createCoaching(Request $request)
    {
        $coaching_log_count = CoachingLog::count();
        $cl_number_initial = STR_PAD($coaching_log_count+1, 9, '0', STR_PAD_LEFT);
        $coaching = new CoachingLog;
        $coaching->id = $coaching_log_count+1;
        $coaching->employee_no = $request->employee_no;
        $coaching->cl_number = $cl_number_initial;
        $coaching->findings = $request->findings;
        $coaching->point_of_disscussion = $request->point_of_disscussion;
        $coaching->action_plans = $request->action_plans;
        $coaching->agents_commitment = $request->agents_commitment;
        $coaching->supervisors_commitment = $request->supervisors_commitment;
        $coaching->date_start = $request->date_start;
        $coaching->date_end = $request->date_end;
        $coaching->date_created = date('Y-m-d h:m:s');
        $coaching->number_occurrence = $request->number_occurrence;
        $coaching->others = $request->others;
        $coaching->performance_review = $request->performance_review;
        $coaching->attachment_id = $request->attachment_id;
        $coaching->form_type = $request->form_type;
        if ($request->offense_id) {
            $coaching->offense_id = $request->offense_id;
        } elseif ($request->offense_multiple) {
            $coaching->offense_id = json_encode($request->offense_multiple);
        } elseif ($request->attendancepointssystem_id) {
            $coaching->offense_id = $request->attendancepointssystem_id;
        }
        $coaching->created_by = auth()->user()->first_name . " " . auth()->user()->last_name;
        $coaching->created_by_employee_id = auth()->user()->id;
        $coaching->save();
        $user = User::where('employee_no', $request->employee_no)->first();
        if (strlen($user->email_address) > 0) {
            EmailCoachingLog::dispatch(
                $coaching->cl_number,
                $user->email_address,
                $coaching->created_at,
                'coaching_response'
            );

            return response()->json($coaching);
        } else {

            return response()->json($coaching);
        }
    }

    public function addForm(Request $request)
    {
        if ($request->form_type == 0) {
            $rating_array = [
                $request->loan_amount_rating_eom,
                $request->loan_amount_rating_w2,
                $request->loan_amount_rating_w3,
                $request->loan_amount_rating_w4
            ];
            $loan_amount_array_data = [
                'target' => (string)$request->loan_amount_target,
                'rating' => serialize($rating_array),
                'computation' => (string)$request->loan_amount_computation,
                'comment' => (string)$request->loan_amount_comment
            ];
            $knowledge_array_data = [
                'target' => (string)$request->knowledge_target,
                'rating' => (string)$request->knowledge_rating,
                'computation' => (string)$request->knowledge_computation,
                'comment' => (string)$request->knowledge_comment
            ];
            $qa_score_array_data = [
                'target' => (string)$request->qa_score_target,
                'rating' => (string)$request->qa_score_rating,
                'computation' => (string)$request->qa_score_computation,
                'comment' => (string)$request->qa_score_comment
            ];
            $coaching_log_array_data = [
                'target' => (string)$request->coaching_log_target,
                'rating' => (string)$request->coaching_log_rating,
                'computation' => (string)$request->coaching_log_computation,
                'comment' => (string)$request->coaching_log_comment
            ];
            $attendance_reliability_array_data = [
                'target' => (string)$request->attendance_reliability_target,
                'rating' => (string)$request->attendance_reliability_rating,
                'computation' => (string)$request->attendance_reliability_computation,
                'comment' => (string)$request->attendance_reliability_comment
            ];
            $loan_amount = LoanAmountKpi::create($loan_amount_array_data);
            $knowledge = KnowledgeKpi::create($knowledge_array_data);
            $qa_score = QaScoreKpi::create($qa_score_array_data);
            $coaching_log = CoachingLogKpi::create($coaching_log_array_data);
            $attendance_reliability = AttendanceReliabilityKpi::create($attendance_reliability_array_data);
            $score_card = ScoreCardKpi::create([
                'stage' => 'Stage 1',
                'loan_amount_kpi_id' => $loan_amount->id,
                'knowledge_kpi_id' => $knowledge->id,
                'qa_score_kpi_id' => $qa_score->id,
                'coaching_log_kpi_id' => $coaching_log->id,
                'attendance_reliability_kpi_id' => $attendance_reliability->id,
                'weakness' => $request->weakness,
                'strengths' => $request->strengths,
                'action_plans' => $request->action_plans
            ]);
            $coaching = CoachingLog::where('cl_number', $request->cl_number)->update([
                'attachment_id' => $score_card->id,
            ]);

            $coaching_log_count = CoachingLog::where('cl_number', $request->cl_number)->first();
        } elseif ($request->form_type == 1) {
            $loan_amount_array_data = [
                'target' => (string)$request->loan_amount_target,
                'rating' => (string)$request->loan_amount_rating,
                'computation' => (string)$request->loan_amount_computation,
                'comment' => (string)$request->loan_amount_comment
            ];
            $qa_compliance_array_data = [
                'target' => (string)$request->qa_compliance_target,
                'rating' => (string)$request->qa_compliance_rating,
                'computation' => (string)$request->qa_compliance_computation,
                'comment' => (string)$request->qa_compliance_comment
            ];
            $qa_score_array_data = [
                'target' => (string)$request->qa_score_target,
                'rating' => (string)$request->qa_score_rating,
                'computation' => (string)$request->qa_score_computation,
                'comment' => (string)$request->qa_score_comment
            ];
            $correction_array_data = [
                'target' => (string)$request->correction_target,
                'rating' => (string)$request->correction_rating,
                'computation' => (string)$request->correction_computation,
                'comment' => (string)$request->correction_comment
            ];
            $attendance_reliability_array_data = [
                'target' => (string)$request->attendance_reliability_target,
                'rating' => (string)$request->attendance_reliability_rating,
                'computation' => (string)$request->attendance_reliability_computation,
                'comment' => (string)$request->attendance_reliability_comment
            ];
            $loan_amount = LoanAmountKpi::create($loan_amount_array_data);
            $qa_compliance = QaComplianceKpi::create($qa_compliance_array_data);
            $qa_score = QaScoreKpi::create($qa_score_array_data);
            $correction = CorrectionKpi::create($correction_array_data);
            $attendance_reliability = AttendanceReliabilityKpi::create($attendance_reliability_array_data);
            $score_card = ScoreCardKpi::create([
                'stage' => $request->stage,
                'loan_amount_kpi_id' => $loan_amount->id,
                'qa_compliance_kpi_id' => $qa_compliance->id,
                'qa_score_kpi_id' => $qa_score->id,
                'correction_kpi_id' => $correction->id,
                'attendance_reliability_kpi_id' => $attendance_reliability->id,
                'weakness' => $request->weakness,
                'strengths' => $request->strengths,
                'action_plans' => $request->action_plans
            ]);
            $coaching = CoachingLog::where('cl_number', $request->cl_number)->update([
                'attachment_id' => $score_card->id,
            ]);

            $coaching_log_count = CoachingLog::where('cl_number', $request->cl_number)->first();
        } elseif ($request->form_type == 2) {
            $admin = new AdminForms;
            $admin->cl_number = $request->cl_number;
            $admin->attendance_reliability_target = $request->attendance_reliability_target;
            $admin->attendance_reliability_actual_rating = $request->attendance_reliability_actual_rating;
            $admin->attendance_reliability_weight_of_kpi = $request->attendance_reliability_weight_of_kpi;
            $admin->attendance_reliability_rating = $request->attendance_reliability_rating;
            $admin->attendance_reliability_comment = $request->attendance_reliability_comment;
            $admin->punctuality_target = $request->punctuality_target;
            $admin->punctuality_actual_rating = $request->punctuality_actual_rating;
            $admin->punctuality_weight_of_kpi = $request->punctuality_weight_of_kpi;
            $admin->punctuality_rating = $request->punctuality_rating;
            $admin->punctuality_comment = $request->punctuality_comment;
            $admin->process_knowledge_target = $request->process_knowledge_target;
            $admin->process_knowledge_actual_rating = $request->process_knowledge_actual_rating;
            $admin->process_knowledge_weight_of_kpi = $request->process_knowledge_weight_of_kpi;
            $admin->process_knowledge_rating = $request->process_knowledge_rating;
            $admin->process_knowledge_comment = $request->process_knowledge_comment;
            $admin->work_ethics_number_target = $request->work_ethics_number_target;
            $admin->work_ethics_number_actual_rating = $request->work_ethics_number_actual_rating;
            $admin->work_ethics_number_weight_of_kpi = $request->work_ethics_number_weight_of_kpi;
            $admin->work_ethics_number_rating = $request->work_ethics_number_rating;
            $admin->work_ethics_number_comment = $request->work_ethics_number_comment;
            $admin->work_ethics_no_nte_target = $request->work_ethics_no_nte_target;
            $admin->work_ethics_no_nte_actual_rating = $request->work_ethics_no_nte_actual_rating;
            $admin->work_ethics_no_nte_weight_of_kpi = $request->work_ethics_no_nte_weight_of_kpi;
            $admin->work_ethics_no_nte_rating = $request->work_ethics_no_nte_rating;
            $admin->work_ethics_no_nte_comment = $request->work_ethics_no_nte_comment;
            $admin->total = $request->total;
            $admin->development_plan = $request->development_plan;
            $admin->weakness = $request->weakness;
            $admin->strengths = $request->strengths;
            $admin->managers_feedback = $request->managers_feedback;
            $admin->employees_acknowledge = $request->employees_acknowledge;
            $admin->save();
        } elseif ($request->form_type == 3) {
            $angel = new HrisForms;
            $angel->cl_number = $request->cl_number;
            $angel->initiative_target = $request->initiative_target;
            $angel->initiative_weight = $request->initiative_weight;
            $angel->initiative_rating_scale = $request->initiative_rating_scale;
            $angel->initiative_final_score = $request->initiative_final_score;
            $angel->work_ethics_target = $request->work_ethics_target;
            $angel->work_ethics_weight = $request->work_ethics_weight;
            $angel->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $angel->work_ethics_final_score = $request->work_ethics_final_score;
            $angel->responsiveness_target = $request->responsiveness_target;
            $angel->responsiveness_weight = $request->responsiveness_weight;
            $angel->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $angel->responsiveness_final_score = $request->responsiveness_final_score;
            $angel->attendance_reliability_target = $request->attendance_reliability_target;
            $angel->attendance_reliability_weight = $request->attendance_reliability_weight;
            $angel->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $angel->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $angel->timely_and_accuraccy_of_on_boarding_target = $request->timely_and_accuraccy_of_on_boarding_target;
            $angel->timely_and_accuraccy_of_on_boarding_weight = $request->timely_and_accuraccy_of_on_boarding_weight;
            $angel->timely_and_accuraccy_of_on_boarding_rating_scale =
                $request->timely_and_accuraccy_of_on_boarding_rating_scale;
            $angel->timely_and_accuraccy_of_on_boarding_final_score =
                $request->timely_and_accuraccy_of_on_boarding_final_score;
            $angel->timely_and_accuraccy_of_off_boarding_target = $request->timely_and_accuraccy_of_off_boarding_target;
            $angel->timely_and_accuraccy_of_off_boarding_weight = $request->timely_and_accuraccy_of_off_boarding_weight;
            $angel->timely_and_accuraccy_of_off_boarding_rating_scale =
                $request->timely_and_accuraccy_of_off_boarding_rating_scale;
            $angel->timely_and_accuraccy_of_off_boarding_final_score =
                $request->timely_and_accuraccy_of_off_boarding_final_score;
            $angel->timely_and_accuraccy_of_updating_in_masterfile_target =
                $request->timely_and_accuraccy_of_updating_in_masterfile_target;
            $angel->timely_and_accuraccy_of_updating_in_masterfile_weight =
                $request->timely_and_accuraccy_of_updating_in_masterfile_weight;
            $angel->timely_and_accuraccy_of_updating_in_masterfile_rating_scale =
                $request->timely_and_accuraccy_of_updating_in_masterfile_rating_scale;
            $angel->timely_and_accuraccy_of_updating_in_masterfile_final_score =
                $request->timely_and_accuraccy_of_updating_in_masterfile_final_score;
            $angel->submission_of_weekly_reports_target = $request->submission_of_weekly_reports_target;
            $angel->submission_of_weekly_reports_weight = $request->submission_of_weekly_reports_weight;
            $angel->submission_of_weekly_reports_rating_scale = $request->submission_of_weekly_reports_rating_scale;
            $angel->submission_of_weekly_reports_final_score = $request->submission_of_weekly_reports_final_score;
            $angel->final_pay_target = $request->final_pay_target;
            $angel->final_pay_weight = $request->final_pay_weight;
            $angel->final_pay_rating_scale = $request->final_pay_rating_scale;
            $angel->final_pay_final_score = $request->final_pay_final_score;
            $angel->number_of_escalation_target = $request->number_of_escalation_target;
            $angel->number_of_escalation_weight = $request->number_of_escalation_weight;
            $angel->number_of_escalation_rating_scale = $request->number_of_escalation_rating_scale;
            $angel->number_of_escalation_final_score = $request->number_of_escalation_final_score;
            $angel->total_score = $request->total_score;
            $angel->commendation = $request->commendation;
            $angel->action_plans = $request->action_plans;
            $angel->save();
        } elseif ($request->form_type == 4) {
            $cnb = new CNBForms;
            $cnb->cl_number = $request->cl_number;
            $cnb->initiative_target = $request->initiative_target;
            $cnb->initiative_weight = $request->initiative_weight;
            $cnb->initiative_rating_scale = $request->initiative_rating_scale;
            $cnb->initiative_final_score = $request->initiative_final_score;
            $cnb->work_ethics_target = $request->work_ethics_target;
            $cnb->work_ethics_weight = $request->work_ethics_weight;
            $cnb->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $cnb->work_ethics_final_score = $request->work_ethics_final_score;
            $cnb->responsiveness_target = $request->responsiveness_target;
            $cnb->responsiveness_weight = $request->responsiveness_weight;
            $cnb->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $cnb->responsiveness_final_score = $request->responsiveness_final_score;
            $cnb->attendance_reliability_target = $request->attendance_reliability_target;
            $cnb->attendance_reliability_weight = $request->attendance_reliability_weight;
            $cnb->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $cnb->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $cnb->payroll_file_target = $request->payroll_file_target;
            $cnb->payroll_file_weight = $request->payroll_file_weight;
            $cnb->payroll_file_rating_scale = $request->payroll_file_rating_scale;
            $cnb->payroll_file_final_score = $request->payroll_file_final_score;
            $cnb->timekeeping_target = $request->timekeeping_target;
            $cnb->timekeeping_weight = $request->timekeeping_weight;
            $cnb->timekeeping_rating_scale = $request->timekeeping_rating_scale;
            $cnb->timekeeping_final_score = $request->timekeeping_final_score;
            $cnb->processing_target = $request->processing_target;
            $cnb->processing_weight = $request->processing_weight;
            $cnb->processing_rating_scale = $request->processing_rating_scale;
            $cnb->processing_final_score = $request->processing_final_score;
            $cnb->hmo_target = $request->hmo_target;
            $cnb->hmo_weight = $request->hmo_weight;
            $cnb->hmo_rating_scale = $request->hmo_rating_scale;
            $cnb->hmo_final_score = $request->hmo_final_score;
            $cnb->inssuance_target = $request->inssuance_target;
            $cnb->inssuance_weight = $request->inssuance_weight;
            $cnb->inssuance_rating_scale = $request->inssuance_rating_scale;
            $cnb->inssuance_final_score = $request->inssuance_final_score;
            $cnb->final_pay_target = $request->final_pay_target;
            $cnb->final_pay_weight = $request->final_pay_weight;
            $cnb->final_pay_rating_scale = $request->final_pay_rating_scale;
            $cnb->final_pay_final_score = $request->final_pay_final_score;
            $cnb->total_score = $request->total_score;
            $cnb->commendation = $request->commendation;
            $cnb->action_plans = $request->action_plans;
            $cnb->save();
        } elseif ($request->form_type == 5) {
            $final_pay = new FinalPayForms;
            $final_pay->cl_number = $request->cl_number;
            $final_pay->initiative_target = $request->initiative_target;
            $final_pay->initiative_weight = $request->initiative_weight;
            $final_pay->initiative_rating_scale = $request->initiative_rating_scale;
            $final_pay->initiative_final_score = $request->initiative_final_score;
            $final_pay->work_ethics_target = $request->work_ethics_target;
            $final_pay->work_ethics_weight = $request->work_ethics_weight;
            $final_pay->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $final_pay->work_ethics_final_score = $request->work_ethics_final_score;
            $final_pay->responsiveness_target = $request->responsiveness_target;
            $final_pay->responsiveness_weight = $request->responsiveness_weight;
            $final_pay->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $final_pay->responsiveness_final_score = $request->responsiveness_final_score;
            $final_pay->attendance_reliability_target = $request->attendance_reliability_target;
            $final_pay->attendance_reliability_weight = $request->attendance_reliability_weight;
            $final_pay->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $final_pay->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $final_pay->accuracy_computation_target = $request->accuracy_computation_target;
            $final_pay->accuracy_computation_weight = $request->accuracy_computation_weight;
            $final_pay->accuracy_computation_rating_scale = $request->accuracy_computation_rating_scale;
            $final_pay->accuracy_computation_final_score = $request->accuracy_computation_final_score;
            $final_pay->timely_target = $request->timely_target;
            $final_pay->timely_weight = $request->timely_weight;
            $final_pay->timely_rating_scale = $request->timely_rating_scale;
            $final_pay->timely_final_score = $request->timely_final_score;
            $final_pay->submission_of_final_rating_target = $request->submission_of_final_rating_target;
            $final_pay->submission_of_final_rating_weight = $request->submission_of_final_rating_weight;
            $final_pay->submission_of_final_rating_scale = $request->submission_of_final_rating_scale;
            $final_pay->submission_of_final_rating_final_score = $request->submission_of_final_rating_final_score;
            $final_pay->number_target = $request->number_target;
            $final_pay->number_weight = $request->number_weight;
            $final_pay->number_rating_scale = $request->number_rating_scale;
            $final_pay->number_final_score = $request->number_final_score;
            $final_pay->total_score = $request->total_score;
            $final_pay->commendation = $request->commendation;
            $final_pay->action_plans = $request->action_plans;
            $final_pay->save();
        } elseif ($request->form_type == 6) {
            $manager_rating_site_lead_forms = new ManagerRatingSiteLeadForms;
            $manager_rating_site_lead_forms->cl_number = $request->cl_number;
            $manager_rating_site_lead_forms->work_ethics_target = $request->work_ethics_target;
            $manager_rating_site_lead_forms->work_ethics_weight = $request->work_ethics_weight;
            $manager_rating_site_lead_forms->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $manager_rating_site_lead_forms->work_ethics_final_score = $request->work_ethics_final_score;
            $manager_rating_site_lead_forms->leadership_target = $request->leadership_target;
            $manager_rating_site_lead_forms->leadership_weight = $request->leadership_weight;
            $manager_rating_site_lead_forms->leadership_rating_scale = $request->leadership_rating_scale;
            $manager_rating_site_lead_forms->leadership_final_score = $request->leadership_final_score;
            $manager_rating_site_lead_forms->attendance_reliability_target = $request->attendance_reliability_target;
            $manager_rating_site_lead_forms->attendance_reliability_weight = $request->attendance_reliability_weight;
            $manager_rating_site_lead_forms->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $manager_rating_site_lead_forms->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $manager_rating_site_lead_forms->monthly_data_target = $request->monthly_data_target;
            $manager_rating_site_lead_forms->monthly_data_weight = $request->monthly_data_weight;
            $manager_rating_site_lead_forms->monthly_data_rating_scale = $request->monthly_data_rating_scale;
            $manager_rating_site_lead_forms->monthly_data_final_score = $request->monthly_data_final_score;
            $manager_rating_site_lead_forms->monthly_site_target = $request->monthly_site_target;
            $manager_rating_site_lead_forms->monthly_site_weight = $request->monthly_site_weight;
            $manager_rating_site_lead_forms->monthly_site_rating_scale = $request->monthly_site_rating_scale;
            $manager_rating_site_lead_forms->monthly_site_final_score = $request->monthly_site_final_score;
            $manager_rating_site_lead_forms->productivity_target = $request->productivity_target;
            $manager_rating_site_lead_forms->productivity_weight = $request->productivity_weight;
            $manager_rating_site_lead_forms->productivity_rating_scale = $request->productivity_rating_scale;
            $manager_rating_site_lead_forms->productivity_final_score = $request->productivity_final_score;
            $manager_rating_site_lead_forms->total_score = $request->total_score;
            $manager_rating_site_lead_forms->commendation = $request->commendation;
            $manager_rating_site_lead_forms->action_plans = $request->action_plans;
            $manager_rating_site_lead_forms->save();
        } elseif ($request->form_type == 7) {
            $onboarding = new OnboardingForms;
            $onboarding->cl_number = $request->cl_number;
            $onboarding->initiative_target = $request->initiative_target;
            $onboarding->initiative_weight = $request->initiative_weight;
            $onboarding->initiative_rating_scale = $request->initiative_rating_scale;
            $onboarding->initiative_final_score = $request->initiative_final_score;
            $onboarding->work_ethics_target = $request->work_ethics_target;
            $onboarding->work_ethics_weight = $request->work_ethics_weight;
            $onboarding->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $onboarding->work_ethics_final_score = $request->work_ethics_final_score;
            $onboarding->responsiveness_target = $request->responsiveness_target;
            $onboarding->responsiveness_weight = $request->responsiveness_weight;
            $onboarding->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $onboarding->responsiveness_final_score = $request->responsiveness_final_score;
            $onboarding->attendance_reliability_target = $request->attendance_reliability_target;
            $onboarding->attendance_reliability_weight = $request->attendance_reliability_weight;
            $onboarding->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $onboarding->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $onboarding->completion_target = $request->completion_target;
            $onboarding->completion_weight = $request->completion_weight;
            $onboarding->completion_rating_scale = $request->completion_rating_scale;
            $onboarding->completion_final_score = $request->completion_final_score;
            $onboarding->submission_target = $request->submission_target;
            $onboarding->submission_weight = $request->submission_weight;
            $onboarding->submission_rating_scale = $request->submission_rating_scale;
            $onboarding->submission_final_score = $request->submission_final_score;
            $onboarding->bank_target = $request->bank_target;
            $onboarding->bank_weight = $request->bank_weight;
            $onboarding->bank_rating_scale = $request->bank_rating_scale;
            $onboarding->bank_final_score = $request->bank_final_score;
            $onboarding->hmo_target = $request->hmo_target;
            $onboarding->hmo_weight = $request->hmo_weight;
            $onboarding->hmo_rating_scale = $request->hmo_rating_scale;
            $onboarding->hmo_final_score = $request->hmo_final_score;
            $onboarding->escalation_target = $request->escalation_target;
            $onboarding->escalation_weight = $request->escalation_weight;
            $onboarding->escalation_rating_scale = $request->escalation_rating_scale;
            $onboarding->escalation_final_score = $request->escalation_final_score;
            $onboarding->inssuance_target = $request->inssuance_target;
            $onboarding->inssuance_weight = $request->inssuance_weight;
            $onboarding->inssuance_rating_scale = $request->inssuance_rating_scale;
            $onboarding->inssuance_final_score = $request->inssuance_final_score;
            $onboarding->submission_by_weekly_target = $request->submission_by_weekly_target;
            $onboarding->submission_by_weekly_weight = $request->submission_by_weekly_weight;
            $onboarding->submission_by_weekly_rating_scale = $request->submission_by_weekly_rating_scale;
            $onboarding->submission_by_weekly_final_score = $request->submission_by_weekly_final_score;
            $onboarding->total_score = $request->total_score;
            $onboarding->commendation = $request->commendation;
            $onboarding->action_plans = $request->action_plans;
            $onboarding->save();
        } elseif ($request->form_type == 8) {
            $payroll = new PayrollForms;
            $payroll->cl_number = $request->cl_number;
            $payroll->initiative_target = $request->initiative_target;
            $payroll->initiative_weight = $request->initiative_weight;
            $payroll->initiative_rating_scale = $request->initiative_rating_scale;
            $payroll->initiative_final_score = $request->initiative_final_score;
            $payroll->work_ethics_target = $request->work_ethics_target;
            $payroll->work_ethics_weight = $request->work_ethics_weight;
            $payroll->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $payroll->work_ethics_final_score = $request->work_ethics_final_score;
            $payroll->responsiveness_target = $request->responsiveness_target;
            $payroll->responsiveness_weight = $request->responsiveness_weight;
            $payroll->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $payroll->responsiveness_final_score = $request->responsiveness_final_score;
            $payroll->attendance_reliability_target = $request->attendance_reliability_target;
            $payroll->attendance_reliability_weight = $request->attendance_reliability_weight;
            $payroll->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $payroll->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $payroll->payroll_reports_target = $request->payroll_reports_target;
            $payroll->payroll_reports_weight = $request->payroll_reports_weight;
            $payroll->payroll_reports_rating_scale = $request->payroll_reports_rating_scale;
            $payroll->payroll_reports_final_score = $request->payroll_reports_final_score;
            $payroll->government_target = $request->government_target;
            $payroll->government_weight = $request->government_weight;
            $payroll->government_rating_scale = $request->government_rating_scale;
            $payroll->government_final_score = $request->government_final_score;
            $payroll->bank_target = $request->bank_target;
            $payroll->bank_weight = $request->bank_weight;
            $payroll->bank_rating_scale = $request->bank_rating_scale;
            $payroll->bank_final_score = $request->bank_final_score;
            $payroll->escalation_target = $request->escalation_target;
            $payroll->escalation_weight = $request->escalation_weight;
            $payroll->escalation_rating_scale = $request->escalation_rating_scale;
            $payroll->escalation_final_score = $request->escalation_final_score;
            $payroll->total_score = $request->total_score;
            $payroll->commendation = $request->commendation;
            $payroll->action_plans = $request->action_plans;
            $payroll->save();
        } elseif ($request->form_type == 9) {
            $recruitment = new RecruitmentForms;
            $recruitment->cl_number = $request->cl_number;
            $recruitment->number_of_hires_target = $request->number_of_hires_target;
            $recruitment->number_of_hires_actual_rating = $request->number_of_hires_actual_rating;
            $recruitment->number_of_hires_weight = $request->number_of_hires_weight;
            $recruitment->number_of_hires_rating_scale = $request->number_of_hires_rating_scale;
            $recruitment->number_of_hires_comment = $request->number_of_hires_comment;
            $recruitment->time_to_fill_target = $request->time_to_fill_target;
            $recruitment->time_to_fill_actual_rating = $request->time_to_fill_actual_rating;
            $recruitment->time_to_fill_weight = $request->time_to_fill_weight;
            $recruitment->time_to_fill_rating_scale = $request->time_to_fill_rating_scale;
            $recruitment->time_to_fill_comment = $request->time_to_fill_comment;
            $recruitment->work_ethics_number_target = $request->work_ethics_number_target;
            $recruitment->work_ethics_number_actual_rating = $request->work_ethics_number_actual_rating;
            $recruitment->work_ethics_number_weight = $request->work_ethics_number_weight;
            $recruitment->work_ethics_number_rating_scale = $request->work_ethics_number_rating_scale;
            $recruitment->work_ethics_number_comment = $request->work_ethics_number_comment;
            $recruitment->work_ethics_nte_target = $request->work_ethics_nte_target;
            $recruitment->work_ethics_nte_actual_rating = $request->work_ethics_nte_actual_rating;
            $recruitment->work_ethics_nte_weight = $request->work_ethics_nte_weight;
            $recruitment->work_ethics_nte_rating_scale = $request->work_ethics_nte_rating_scale;
            $recruitment->work_ethics_nte_comment = $request->work_ethics_nte_comment;
            $recruitment->recruitment_target = $request->recruitment_target;
            $recruitment->recruitment_actual_rating = $request->recruitment_actual_rating;
            $recruitment->recruitment_weight = $request->recruitment_weight;
            $recruitment->recruitment_rating_scale = $request->recruitment_rating_scale;
            $recruitment->recruitment_comment = $request->recruitment_comment;
            $recruitment->total_score = $request->total_score;
            $recruitment->development_plan = $request->development_plan;
            $recruitment->strengths = $request->strengths;
            $recruitment->managers_feedback = $request->managers_feedback;
            $recruitment->employees_acknowledge = $request->employees_acknowledge;
            $recruitment->save();
        } elseif ($request->form_type == 10) {
            $self_rating_hrbp = new SelfRatingHrbpForms;
            $self_rating_hrbp->cl_number = $request->cl_number;
            $self_rating_hrbp->critical_thinking_target = $request->critical_thinking_target;
            $self_rating_hrbp->critical_thinking_weight = $request->critical_thinking_weight;
            $self_rating_hrbp->critical_thinking_rating_scale = $request->critical_thinking_rating_scale;
            $self_rating_hrbp->critical_thinking_final_score = $request->critical_thinking_final_score;
            $self_rating_hrbp->initiative_target = $request->initiative_target;
            $self_rating_hrbp->initiative_weight = $request->initiative_weight;
            $self_rating_hrbp->initiative_rating_scale = $request->initiative_rating_scale;
            $self_rating_hrbp->initiative_final_score = $request->initiative_final_score;
            $self_rating_hrbp->work_ethics_target = $request->work_ethics_target;
            $self_rating_hrbp->work_ethics_weight = $request->work_ethics_weight;
            $self_rating_hrbp->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $self_rating_hrbp->work_ethics_final_score = $request->work_ethics_final_score;
            $self_rating_hrbp->responsiveness_target = $request->responsiveness_target;
            $self_rating_hrbp->responsiveness_weight = $request->responsiveness_weight;
            $self_rating_hrbp->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $self_rating_hrbp->responsiveness_final_score = $request->responsiveness_final_score;
            $self_rating_hrbp->attendance_reliability_target = $request->attendance_reliability_target;
            $self_rating_hrbp->attendance_reliability_weight = $request->attendance_reliability_weight;
            $self_rating_hrbp->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $self_rating_hrbp->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $self_rating_hrbp->data_target = $request->data_target;
            $self_rating_hrbp->data_weight = $request->data_weight;
            $self_rating_hrbp->data_rating_scale = $request->data_rating_scale;
            $self_rating_hrbp->data_final_score = $request->data_final_score;
            $self_rating_hrbp->intervention_target = $request->intervention_target;
            $self_rating_hrbp->intervention_weight = $request->intervention_weight;
            $self_rating_hrbp->intervention_rating_scale = $request->intervention_rating_scale;
            $self_rating_hrbp->intervention_final_score = $request->intervention_final_score;
            $self_rating_hrbp->cluster_target = $request->cluster_target;
            $self_rating_hrbp->cluster_weight = $request->cluster_weight;
            $self_rating_hrbp->cluster_rating_scale = $request->cluster_rating_scale;
            $self_rating_hrbp->cluster_final_score = $request->cluster_final_score;
            $self_rating_hrbp->productivity_target = $request->productivity_target;
            $self_rating_hrbp->productivity_weight = $request->productivity_weight;
            $self_rating_hrbp->productivity_rating_scale = $request->productivity_rating_scale;
            $self_rating_hrbp->productivity_final_score = $request->productivity_final_score;
            $self_rating_hrbp->total_score = $request->total_score;
            $self_rating_hrbp->commendation = $request->commendation;
            $self_rating_hrbp->action_plans = $request->action_plans;
            $self_rating_hrbp->save();
        } elseif ($request->form_type == 11) {
            $self_rating_hrbp_site_lead = new SelfRatingHrbpSiteLeadForms;
            $self_rating_hrbp_site_lead->cl_number = $request->cl_number;
            $self_rating_hrbp_site_lead->work_ethics_target = $request->work_ethics_target;
            $self_rating_hrbp_site_lead->work_ethics_weight = $request->work_ethics_weight;
            $self_rating_hrbp_site_lead->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $self_rating_hrbp_site_lead->work_ethics_final_score = $request->work_ethics_final_score;
            $self_rating_hrbp_site_lead->leadership_target = $request->leadership_target;
            $self_rating_hrbp_site_lead->leadership_weight = $request->leadership_weight;
            $self_rating_hrbp_site_lead->leadership_rating_scale = $request->leadership_rating_scale;
            $self_rating_hrbp_site_lead->leadership_final_score = $request->leadership_final_score;
            $self_rating_hrbp_site_lead->attendance_reliability_target = $request->attendance_reliability_target;
            $self_rating_hrbp_site_lead->attendance_reliability_weight = $request->attendance_reliability_weight;
            $self_rating_hrbp_site_lead->attendance_reliability_rating_scale = $request->attendance_reliability_rating_scale;
            $self_rating_hrbp_site_lead->attendance_reliability_final_score = $request->attendance_reliability_final_score;
            $self_rating_hrbp_site_lead->analysis_target = $request->analysis_target;
            $self_rating_hrbp_site_lead->analysis_weight = $request->analysis_weight;
            $self_rating_hrbp_site_lead->analysis_rating_scale = $request->analysis_rating_scale;
            $self_rating_hrbp_site_lead->analysis_final_score = $request->analysis_final_score;
            $self_rating_hrbp_site_lead->satisfaction_target = $request->satisfaction_target;
            $self_rating_hrbp_site_lead->satisfaction_weight = $request->satisfaction_weight;
            $self_rating_hrbp_site_lead->satisfaction_rating_scale = $request->satisfaction_rating_scale;
            $self_rating_hrbp_site_lead->satisfaction_final_score = $request->satisfaction_final_score;
            $self_rating_hrbp_site_lead->productivity_target = $request->productivity_target;
            $self_rating_hrbp_site_lead->productivity_weight = $request->productivity_weight;
            $self_rating_hrbp_site_lead->productivity_rating_scale = $request->productivity_rating_scale;
            $self_rating_hrbp_site_lead->productivity_final_score = $request->productivity_final_score;
            $self_rating_hrbp_site_lead->total_score = $request->total_score;
            $self_rating_hrbp_site_lead->commendation = $request->commendation;
            $self_rating_hrbp_site_lead->action_plans = $request->action_plans;
            $self_rating_hrbp_site_lead->save();
        } elseif ($request->form_type == 12) {
            $sourcing = new SourcingForms;
            $sourcing->cl_number = $request->cl_number;
            $sourcing->show_up_target = $request->show_up_target;
            $sourcing->show_up_actual_rating = $request->show_up_actual_rating;
            $sourcing->show_up_weight = $request->show_up_weight;
            $sourcing->show_up_rating_scale = $request->show_up_rating_scale;
            $sourcing->show_up_comment = $request->show_up_comment;
            $sourcing->time_to_fill_target = $request->time_to_fill_target;
            $sourcing->time_to_fill_actual_rating = $request->time_to_fill_actual_rating;
            $sourcing->time_to_fill_weight = $request->time_to_fill_weight;
            $sourcing->time_to_fill_rating_scale = $request->time_to_fill_rating_scale;
            $sourcing->time_to_fill_comment = $request->time_to_fill_comment;
            $sourcing->escalation_target = $request->escalation_target;
            $sourcing->escalation_actual_rating = $request->escalation_actual_rating;
            $sourcing->escalation_weight = $request->escalation_weight;
            $sourcing->escalation_rating_scale = $request->escalation_rating_scale;
            $sourcing->escalation_comment = $request->escalation_comment;
            $sourcing->no_nte_target = $request->no_nte_target;
            $sourcing->no_nte_actual_rating = $request->no_nte_actual_rating;
            $sourcing->no_nte_weight = $request->no_nte_weight;
            $sourcing->no_nte_rating_scale = $request->no_nte_rating_scale;
            $sourcing->no_nte_comment = $request->no_nte_comment;
            $sourcing->initiative_target = $request->initiative_target;
            $sourcing->initiative_actual_rating = $request->initiative_actual_rating;
            $sourcing->initiative_weight = $request->initiative_weight;
            $sourcing->initiative_rating_scale = $request->initiative_rating_scale;
            $sourcing->initiative_comment = $request->initiative_comment;
            $sourcing->total_score = $request->total_score;
            $sourcing->development_plan = $request->development_plan;
            $sourcing->weaknesses = $request->weaknesses;
            $sourcing->strengths = $request->strengths;
            $sourcing->managers_feedback = $request->managers_feedback;
            $sourcing->employees_acknowledge = $request->employees_acknowledge;
            $sourcing->save();
        } elseif ($request->form_type == 13) {
            $recruitment_supervisor = new SupervisorRecruitmentForms;
            $recruitment_supervisor->cl_number = $request->cl_number;
            $recruitment_supervisor->support_hiring_target = $request->support_hiring_target;
            $recruitment_supervisor->support_hiring_actual_rating = $request->support_hiring_actual_rating;
            $recruitment_supervisor->support_hiring_weight = $request->support_hiring_weight;
            $recruitment_supervisor->support_hiring_rating_scale = $request->support_hiring_rating_scale;
            $recruitment_supervisor->support_hiring_comments = $request->support_hiring_comments;
            $recruitment_supervisor->agent_hiring_target = $request->agent_hiring_target;
            $recruitment_supervisor->agent_hiring_actual_rating = $request->agent_hiring_actual_rating;
            $recruitment_supervisor->agent_hiring_weight = $request->agent_hiring_weight;
            $recruitment_supervisor->agent_hiring_rating_scale = $request->agent_hiring_rating_scale;
            $recruitment_supervisor->agent_hiring_comments = $request->agent_hiring_comments;
            $recruitment_supervisor->development_target = $request->development_target;
            $recruitment_supervisor->development_actual_rating = $request->development_actual_rating;
            $recruitment_supervisor->development_weight = $request->development_weight;
            $recruitment_supervisor->development_rating_scale = $request->development_rating_scale;
            $recruitment_supervisor->development_comments = $request->development_comments;
            $recruitment_supervisor->escalation_target = $request->escalation_target;
            $recruitment_supervisor->escalation_actual_rating = $request->escalation_actual_rating;
            $recruitment_supervisor->escalation_weight = $request->escalation_weight;
            $recruitment_supervisor->escalation_rating_scale = $request->escalation_rating_scale;
            $recruitment_supervisor->escalation_comments = $request->escalation_comments;
            $recruitment_supervisor->infraction_target = $request->infraction_target;
            $recruitment_supervisor->infraction_actual_rating = $request->infraction_actual_rating;
            $recruitment_supervisor->infraction_weight = $request->infraction_weight;
            $recruitment_supervisor->infraction_rating_scale = $request->infraction_rating_scale;
            $recruitment_supervisor->infraction_comments = $request->infraction_comments;
            $recruitment_supervisor->recruitment_teams_target = $request->recruitment_teams_target;
            $recruitment_supervisor->recruitment_teams_actual_rating = $request->recruitment_teams_actual_rating;
            $recruitment_supervisor->recruitment_teams_weight = $request->recruitment_teams_weight;
            $recruitment_supervisor->recruitment_teams_rating_scale = $request->recruitment_teams_rating_scale;
            $recruitment_supervisor->recruitment_teams_comments = $request->recruitment_teams_comments;
            $recruitment_supervisor->total_score = $request->total_score;
            $recruitment_supervisor->development_plan = $request->development_plan;
            $recruitment_supervisor->weaknesses = $request->weaknesses;
            $recruitment_supervisor->strengths = $request->strengths;
            $recruitment_supervisor->managers_feedback = $request->managers_feedback;
            $recruitment_supervisor->employees_acknowledge = $request->employees_acknowledge;
            $recruitment_supervisor->save();
        } elseif ($request->form_type == 14) {
            $supervisor_hrbp = new SupervisorHrbpForms;
            $supervisor_hrbp->cl_number = $request->cl_number;
            $supervisor_hrbp->self_thinking_target = $request->self_thinking_target;
            $supervisor_hrbp->self_thinking_weight = $request->self_thinking_weight;
            $supervisor_hrbp->self_thinking_rating_scale = $request->self_thinking_rating_scale;
            $supervisor_hrbp->self_thinking_final_score = $request->self_thinking_final_score;
            $supervisor_hrbp->initiative_target = $request->initiative_target;
            $supervisor_hrbp->initiative_weight = $request->initiative_weight;
            $supervisor_hrbp->initiative_rating_scale = $request->initiative_rating_scale;
            $supervisor_hrbp->initiative_final_score = $request->initiative_final_score;
            $supervisor_hrbp->work_ethics_target = $request->work_ethics_target;
            $supervisor_hrbp->work_ethics_weight = $request->work_ethics_weight;
            $supervisor_hrbp->work_ethics_rating_scale = $request->work_ethics_rating_scale;
            $supervisor_hrbp->work_ethics_final_score = $request->work_ethics_final_score;
            $supervisor_hrbp->responsiveness_target = $request->responsiveness_target;
            $supervisor_hrbp->responsiveness_weight = $request->responsiveness_weight;
            $supervisor_hrbp->responsiveness_rating_scale = $request->responsiveness_rating_scale;
            $supervisor_hrbp->responsiveness_final_score = $request->responsiveness_final_score;
            $supervisor_hrbp->attendance_target = $request->attendance_target;
            $supervisor_hrbp->attendance_weight = $request->attendance_weight;
            $supervisor_hrbp->attendance_rating_scale = $request->attendance_rating_scale;
            $supervisor_hrbp->attendance_final_score = $request->attendance_final_score;
            $supervisor_hrbp->data_target = $request->data_target;
            $supervisor_hrbp->data_weight = $request->data_weight;
            $supervisor_hrbp->data_rating_scale = $request->data_rating_scale;
            $supervisor_hrbp->data_final_score = $request->data_final_score;
            $supervisor_hrbp->intervention_target = $request->intervention_target;
            $supervisor_hrbp->intervention_weight = $request->intervention_weight;
            $supervisor_hrbp->intervention_rating_scale = $request->intervention_rating_scale;
            $supervisor_hrbp->intervention_final_score = $request->intervention_final_score;
            $supervisor_hrbp->cluster_target = $request->cluster_target;
            $supervisor_hrbp->cluster_weight = $request->cluster_weight;
            $supervisor_hrbp->cluster_rating_scale = $request->cluster_rating_scale;
            $supervisor_hrbp->cluster_final_score = $request->cluster_final_score;
            $supervisor_hrbp->total_score = $request->total_score;
            $supervisor_hrbp->commendation = $request->commendation;
            $supervisor_hrbp->action_plans = $request->action_plans;
            $supervisor_hrbp->save();
        }
    }

    public function updateCoaching(Request $request)
    {
        $coaching = CoachingLog::where('id', $request->id)->update([
            'findings' => $request->findings,
            'point_of_disscussion' => $request->point_of_disscussion,
            'action_plans' => $request->action_plans,
            'agents_commitment' => $request->agents_commitment,
            'supervisors_commitment' => $request->supervisors_commitment,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'number_occurrence' => $request->number_occurrence,
        ]);

        return response()->json($coaching);
    }

    public function coaching()
    {
        $user = auth()->user();
        ini_set('memory_limit', '4095M');

        $coaching = CoachingLog::with([
            'user_employee.employee_supervisor.supervisor_assign',
            'performance.weight',
            'performance.current_score',
            'performance.rating_right',
            'performance.rating_left',
            'performance.week_one',
            'performance.week_two',
            'performance.week_three',
            'performance.week_four',
            'performance.week_five'
        ]);

        if ($user->roles[0]->name != 'Regular User Access') {
            $coaching = $coaching->orderBy('cl_number', 'desc')
            ->paginate(10);
        } else {
            $coaching = $coaching->where('employee_no', $user->employee_no)
            ->orderBy('cl_number', 'desc')
            ->paginate(10);
        }

        return response()->json($coaching);
    }

    public function employeeNameWithEmployeeNo(int $month): JsonResponse
    {
        $dashboard_data = DashboardRaw::whereMonth('report_date', $month)
            ->where('update_status', '!=', 1)
            ->select('employee_no')
            ->get()
            ->groupBy('employee_no');
        $all_employee_no = [];
        foreach ($dashboard_data as $key => $value) {
            array_push($all_employee_no, $key);
        }
        $user = User::whereIn('employee_no', $all_employee_no)->select(
            'id as value',
            DB::raw('CONCAT(TRIM(last_name), " ", first_name, " -- Employee Number: ", employee_no) AS text')
        )
            ->where('id', '!=', Auth()->id())
            ->orderByRaw('TRIM(last_name)')
            ->get();

        return response()->json($user);
    }

    public function employeeNameWithEmployeeNoWithoutFilter(): JsonResponse
    {
        $user = User::select(
            'id as value',
            DB::raw('CONCAT(TRIM(last_name), " ", first_name, " -- Employee Number: ", employee_no) AS text')
        )
            ->where('id', '!=', Auth()->id())
            ->orderByRaw('TRIM(last_name)')
            ->get();

        return response()->json($user);
    }

    public function efficiencyGoal()
    {
        $data = EfficiencyGoal::all();

        return response()->json($data);
    }

    public function averagePerWeek($arr)
    {
        $average = [];
        $real_average = [];
        for ($i=1; $i < 5; $i++) {
            $filter_arr_week = array_filter($arr['Week '.$i]);
            $average_week1 = array_sum($filter_arr_week)/count($filter_arr_week);
            if (is_nan($average_week1)) {
                array_push($average, "0%");
            } else {
                array_push($average, number_format((float)$average_week1, 2, '.', '')."%");
            }
        }

        return $average;
    }

    public function containsOnlyNull($input)
    {
        $contain = empty(array_filter($input, function ($a) {
            return $a !== null;
        }));

        return $contain;
    }

    public function averagePerWeekly($arr)
    {
        $average = [];
        $real_average = [];
        for ($i=1; $i < 5; $i++) {
            $average_week1 = NAN;
            if (!$this->containsOnlyNull($arr['Week '.$i])) {
                $filter_arr_week = array_filter($arr['Week '.$i]);
                $average_week1 = $filter_arr_week ?
                    array_sum($filter_arr_week)/count($filter_arr_week) : '0';
                if (is_nan($average_week1)) {
                    array_push($average, "");
                } else {
                    array_push($average, number_format((float)$average_week1, 2, '.', '')."%");
                }
            } else {
                if (is_nan($average_week1)) {
                    array_push($average, "");
                } else {
                    array_push($average, number_format((float)$average_week1, 2, '.', '')."%");
                }
            }
        }

        return $average;
    }

    public function average($arr)
    {
        $filter = array_filter($arr);
        $average = array_sum($filter)/count($filter);

        return $average;
    }

    public function mergeSameKey($array)
    {
        foreach ($array as $arr) {
            foreach ($arr as $key => $val) {
                $result[$key][] = $val;
            }
        }

        return $result;
    }

    public function sumOfTheSameKey($array)
    {
        $sum_arr = [];

        for ($i=1; $i < 5; $i++) {
            $sum1 = array_sum($array['Week '.$i]);
            array_push($sum_arr, $sum1);
        }

        return $sum_arr;
    }

    public function removeDuplicateData($arr)
    {
        $array_data = [];

        for ($i=1; $i < 5; $i++) {
            $data = array_unique($arr['Week '.$i]);
            array_push($array_data, $data);
        }

        return $array_data;
    }

    public function roundToNearestHundredths($number)
    {
        $round = round($number, -2);

        if ($number > $round) {
            $round = $round + 100;
        }

        return $round;
    }

    public function getLoanAmountScore($score)
    {
        if ($score >= 150) {
            $loan_amount_score = 5;
            $loan_amount_point = $loan_amount_score * .5;
        } elseif ($score <= 149.99 && $score >= 125) {
            $loan_amount_score = 4;
            $loan_amount_point = $loan_amount_score * .5;
        } elseif ($score <= 124.99 && $score >= 100) {
            $loan_amount_score = 3;
            $loan_amount_point = $loan_amount_score * .5;
        } elseif ($score <= 99.99 && $score >= 75) {
            $loan_amount_score = 2;
            $loan_amount_point = $loan_amount_score * .5;
        } else {
            $loan_amount_score = 1;
            $loan_amount_point = $loan_amount_score * .5;
        }

        return [$loan_amount_score,$loan_amount_point];
    }

    public function getAttendanceScore($score)
    {
        if ($score >= 100) {
            $attendance_score = 5;
            $attendance_point = $attendance_score * .2;
        } elseif ($score <= 99.99 && $score >= 95.01) {
            $attendance_score = 4;
            $attendance_point = $attendance_score * .2;
        } elseif ($score == 95) {
            $attendance_score = 3;
            $attendance_point = $attendance_score * .2;
        } elseif ($score <= 94.99 && $score >= 91) {
            $attendance_score = 2;
            $attendance_point = $attendance_score * .2;
        } else {
            $attendance_score = 1;
            $attendance_point = $attendance_score * .2;
        }

        return [$attendance_score,$attendance_point];
    }

    public function getAttendanceScoreArray($arr)
    {
        $attandance_array = [];
        for ($i=0; $i < 4; $i++) {
            $filter_data_arr = $arr[$i];
            if ($filter_data_arr === null) {
                array_push($attandance_array, "0%");
            } else {
                array_push($attandance_array, $filter_data_arr."%");
            }
        }

        return $attandance_array;
    }

    public function getAttendanceScoreArrayWeekly($arr)
    {
        $attandance_array = [];
        for ($i=0; $i < 4; $i++) {
            $filter_data_arr = $arr[$i];
            if ($filter_data_arr === null) {
                array_push($attandance_array, "");
            } else {
                array_push($attandance_array, $filter_data_arr);
            }
        }

        return $attandance_array;
    }

    public function average_per_week($arr)
    {
        $average_per_week = [];
        foreach ($arr as $key => $value) {
            $val_per_week = str_replace('%', '', $value);
            $per_week_ave = $val_per_week*100;
            array_push($average_per_week, $per_week_ave.'%');
        }

        return $average_per_week;
    }

    public function createMonthlyForm($employee_no, $month)
    {
        $dashboard_raw_data = DashboardRaw::where('employee_no', $employee_no)
            ->where('update_status', '!=', 1)
            ->whereMonth('report_date', $month)
            ->orderBy('report_date','desc')
            ->get();
        $loan_amount = DashboardRaw::where('employee_no', $employee_no)
            ->where('update_status', '!=', 1)
            ->whereMonth('report_date', $month)
            ->get();
        $user_data = User::where('employee_no', $employee_no)->with(
            'employee_supervisor',
            'department',
            'designation'
        )
        ->first();
        $supervisor_data = User::where('employee_no', $user_data->employee_supervisor->teamlead_employee_no)->first();
        $dashboard_raw = [];
        $scg_raw = [];
        $coverage_total_loan_amount_raw = [];
        $scorecard_goal_raw = [];
        $loan_amount_raw = [];
        $in_call_raw = [];
        $ready_raw = [];
        $not_ready_raw = [];
        $wrap_up_raw = [];
        $mtdg_raw = [];

        foreach ($loan_amount as $key => $value) {
            array_push($loan_amount_raw, [$value->week_number =>  $value->loan_amount]);
        }
        foreach ($dashboard_raw_data as $key => $value) {
            array_push($scg_raw, [$value->week_number =>  $value->scg]);
            array_push($in_call_raw, [$value->week_number =>  $value->in_call]);
            array_push($ready_raw, [$value->week_number => $value->ready]);
            array_push($not_ready_raw, [$value->week_number => $value->not_ready]);
            array_push($wrap_up_raw, [$value->week_number => $value->wrap_up]);
            array_push($mtdg_raw, [$value->week_number =>  $value->mtdg]);
        }
        $in_call = $this->averagePerWeek($this->mergeSameKey($in_call_raw));
        $ready = $this->averagePerWeek($this->mergeSameKey($ready_raw));
        $not_ready = $this->averagePerWeek($this->mergeSameKey($not_ready_raw));
        $wrap_up = $this->averagePerWeek($this->mergeSameKey($wrap_up_raw));
        $loan_amount = $this->sumOfTheSameKey($this->mergeSameKey($loan_amount_raw));
        $scg = $this->removeDuplicateData($this->mergeSameKey($scg_raw));
        $mtdg = $this->removeDuplicateData($this->mergeSameKey($mtdg_raw));
        $last_scg = end(array_filter($scg));
        $last_mtdg = end(array_filter($mtdg));
        // $numbers_last_scg = preg_replace('/[^0-9]/', '', $last_scg);
        $numbers_last_mtdg = preg_replace('/[^0-9]/', '', $last_mtdg);
        // if ($numbers_last_scg < array_sum($loan_amount)) {
        //     $loan_amount_ave = 100;
        // } else {
        //     $loan_amount_ave = (array_sum($loan_amount)/$last_scg[0])*100;
        // }
        if ($numbers_last_mtdg < array_sum($loan_amount)) {
            $loan_amount_ave = 100;
        } else {
            $loan_amount_ave = (array_sum($loan_amount)/$last_mtdg[0])*100;
        }

        $loan_amount_score = $this->getLoanAmountScore($loan_amount_ave);
        $supervisor_check = $supervisor_data ?
        $supervisor_data->last_name. ", ".$supervisor_data->first_name : "No Supervisor in VPS record";
        $attendance_reliability = DashboardRaw::where('employee_no', $employee_no)
            ->where('update_status', '!=', 1)
            ->whereMonth('report_date', $month)
            ->orderBy('report_date', 'DECS')
            ->get();
        $grouped = $attendance_reliability->groupBy('week_number');
        $attendance = [];
        for ($i=1; $i < 5; $i++) {
            if ($grouped['Week '.$i] === null) {
                array_push($attendance, '');
            } else {
                array_push($attendance, ($grouped['Week '.$i][0]['attendance_reliability']*100)."%");
            }
        }
        $days = DashboardRaw::where('employee_no', $employee_no)
            ->where('update_status', '!=', 1)
            ->whereMonth('report_date', $month)
            ->first();

        $attendance_reliability_ave = end(array_filter($attendance));
        // $average_attendance = array_sum($this->getAttendanceScoreArray(array_filter($attendance))) / count(array_filter($attendance));
        // dd($average_attendance);
        // exit();
        $attendance_score = $this->getAttendanceScore($attendance_reliability_ave);
        // $attendance_ave = (($average_attendance*100)/100)*20;
        $dateNow = Carbon::now();
        $dateSet = Carbon::createFromFormat('Y-m-d H:i:s', $dateNow, 'Europe/Stockholm');
        $dateSet->setTimezone('Asia/Manila');

        $dashboard_data = [
            'date' => $dateSet->format('Y-m-d'),
            'name' => $user_data->last_name. ", " .$user_data->first_name,
            // 'scg' => $scg,
            'mtdg' => $mtdg,
            'employee_no' => $employee_no,
            'attendance_reliability' => $attendance,
            'attendance_reliability_ave' => $attendance_reliability_ave,
            'days' => $days->days ? $days->days : '-',
            'supervisor' => $supervisor_check,
            'department' => $user_data->department->name,
            'designation' => $user_data->designation->name,
            'tenureship' => $dashboard_raw_data[0]->tenureship,
            'coverage' => $dashboard_raw_data[0]->coverage,
            'portfolio' => $dashboard_raw_data[0]->portfolio,
            'in_call' => $this->average_per_week($in_call),
            // 'end_scg' => $numbers_last_scg,
            'end_mtdg' => $numbers_last_mtdg,
            'ready' => $this->average_per_week($ready),
            'not_ready' => $this->average_per_week($not_ready),
            'wrap_up' => $this->average_per_week($wrap_up),
            'in_call_ave' => $this->average($in_call),
            'ready_ave' => $this->average($ready),
            'not_ready_ave' => $this->average($not_ready),
            'wrap_up_ave' => $this->average($wrap_up),
            'in_call_tag' => $dashboard_raw_data[0]->in_call_target,
            'ready_tag' => $dashboard_raw_data[0]->ready_target,
            'not_ready_tag' => $dashboard_raw_data[0]->not_ready_target,
            'wrap_up_tag' => $dashboard_raw_data[0]->wrap_up_target,
            'loan_amount' => $loan_amount,
            'loan_amount_score' => $loan_amount_score[0],
            'loan_amount_point' => $loan_amount_score[1],
            'attendance_score' => $attendance_score[0],
            'attendance_point' => $attendance_score[1],
            'loan_amount_ave' => number_format($loan_amount_ave, 2, '.', '')
        ];
        $dashboard_save_db = [
            'name' => $user_data->last_name. ", " .$user_data->first_name,
            // 'scg' => $scg,
            'mtdg' => $mtdg,
            'employee_no' => $employee_no,
            'attendance_reliability' => $attendance,
            'attendance_reliability_ave' => $attendance_reliability_ave,
            'days' => $days->days ? $days->days : '-',
            'supervisor' => $supervisor_check,
            'department' => $user_data->department->name,
            'designation' => $user_data->designation->name,
            'tenureship' => $dashboard_raw_data[0]->tenureship,
            'coverage' => $dashboard_raw_data[0]->coverage,
            'portfolio' => $dashboard_raw_data[0]->portfolio,
            'in_call' => $this->average_per_week($in_call),
            // 'end_scg' => $numbers_last_scg,
            'end_mtdg' => $numbers_last_mtdg,
            'ready' => $this->average_per_week($ready),
            'not_ready' => $this->average_per_week($not_ready),
            'wrap_up' => $this->average_per_week($wrap_up),
            'in_call_ave' => $this->average($in_call),
            'ready_ave' => $this->average($ready),
            'not_ready_ave' => $this->average($not_ready),
            'wrap_up_ave' => $this->average($wrap_up),
            'in_call_tag' => $dashboard_raw_data[0]->in_call_target,
            'ready_tag' => $dashboard_raw_data[0]->ready_target,
            'not_ready_tag' => $dashboard_raw_data[0]->not_ready_target,
            'wrap_up_tag' => $dashboard_raw_data[0]->wrap_up_target,
            'loan_amount' => serialize($loan_amount),
            'loan_amount_score' => $loan_amount_score[0],
            'loan_amount_point' => $loan_amount_score[1],
            'attendance_score' => $attendance_score[0],
            'attendance_point' => $attendance_score[1],
            'weekly' => 0,
            'monthly' => 1,
            'loan_amount_ave' => number_format($loan_amount_ave, 2, '.', ''),
            'created_by' => auth()->user()->first_name . " " . auth()->user()->last_name,
            'date_created' => $dateSet->format('Y-m-d h:m:s'),
        ];
        MonthlyPerformance::create($dashboard_save_db);
        $filename = "MPB_". str_replace(' ', '_', $user_data->last_name)
                . "_". str_replace(' ', '_', $user_data->first_name)
                . "_" . $employee_no;

        $pdf = PDF::loadView('pdf.generate.coaching_log.createMonthlyForm', $dashboard_data);

        return $pdf->download($filename.'.pdf');
    }

    public function getWeeklyStatus($weekly)
    {
        $week = [];
        if ($weekly === 'Week 1') {
            $week = [
                'Week 1'
            ];
        } elseif ($weekly === 'Week 2') {
            $week = [
                'Week 1',
                'Week 2'
            ];
        } elseif ($weekly === 'Week 3') {
            $week = [
                'Week 1',
                'Week 2',
                'Week 3'
            ];
        } elseif ($weekly === 'Week 4') {
            $week = [
                'Week 1',
                'Week 2',
                'Week 3',
                'Week 4'
            ];
        }

        return $week;
    }

    public function getSCG($scg)
    {
        $final_scg = [];
        foreach ($scg as $key => $value) {
            if ($value[0]/4 !== 0) {
                array_push($final_scg, $value[0]/4);
            }
            if ($value[0]/4 === 0) {
                array_push($final_scg, "");
            }
        }

        return $final_scg;
    }

    public function getMTDG($mtdg)
    {
        $final_mtdg =[];
        foreach ($mtdg as $key => $value) {
            // echo $value[0]."<br>";
            if ($value[0]/4 !== 0) {
                array_push($final_mtdg, $value[0]/4);
            }
            if ($value[0]/4 === 0) {
                array_push($final_mtdg, "");
            }
        }

        // exit();

        return $final_mtdg;
    }

    public function getTarget($arr, $count)
    {
        $data = [];
        $target_in_call = [];
        $target_ready = [];
        $target_not_ready = [];
        $target_wrap_up = [];
        for ($i=0; $i < $count[1]; $i++) {
            array_push($target_in_call, $arr['in_call_tag']."%");
            array_push($target_ready, $arr['ready_tag']."%");
            array_push($target_not_ready, $arr['not_ready_tag']."%");
            array_push($target_wrap_up, $arr['wrap_up_tag']."%");
        }
        $data = [
            'target_in_call' => $target_in_call,
            'target_ready' => $target_ready,
            'target_not_ready' => $target_not_ready,
            'target_wrap_up' => $target_wrap_up
        ];

        return $data;
    }

    public function getLoanAmmount($loan)
    {
        $data = [];
        foreach ($loan as $key => $value) {
            if (is_null($value)) {
                array_push($data, '');
            } elseif ($value === 0) {
                array_push($data, 0);
            } elseif ($value >= 0) {
                array_push($data, $value);
            } else {
                array_push($data, $value);
            }
        }

        return $data;
    }

    public function createWeeklyForm($employee_no, $month, $weekly)
    {
        $week = $this->getWeeklyStatus($weekly);
        $loop = explode(" ", $weekly);

        $dashboard_raw_data = DashboardRaw::whereIn('week_number', $week)
            ->where('update_status', '!=', 1)
            ->where('employee_no', $employee_no)
            ->whereMonth('report_date', $month)
            ->orderBy('report_date','desc')
            ->get();

        $target_arr = [
            'in_call_tag' => $dashboard_raw_data[0]->in_call_target,
            'ready_tag' => $dashboard_raw_data[0]->ready_target,
            'not_ready_tag' => $dashboard_raw_data[0]->not_ready_target,
            'wrap_up_tag' => $dashboard_raw_data[0]->wrap_up_target
        ];
        $target = $this->getTarget($target_arr, $loop);
        $user_data = User::where('employee_no', $employee_no)->with(
            'employee_supervisor',
            'department',
            'designation'
        )
        ->first();
        $supervisor_data = User::where('employee_no', $user_data->employee_supervisor->teamlead_employee_no)->first();
        $dashboard_raw = [];
        $scg_raw = [];
        $coverage_total_loan_amount_raw = [];
        $scorecard_goal_raw = [];
        $loan_amount_raw = [];
        $in_call_raw = [];
        $ready_raw = [];
        $not_ready_raw = [];
        $wrap_up_raw = [];
        $scg_raw = [];
        $mtdg_raw = [];

        foreach ($dashboard_raw_data as $key => $value) {
            array_push($scg_raw, [$value->week_number =>  $value->scg]);
            array_push($loan_amount_raw, [$value->week_number =>  $value->loan_amount]);
            array_push($in_call_raw, [$value->week_number =>  $value->in_call]);
            array_push($ready_raw, [$value->week_number => $value->ready]);
            array_push($not_ready_raw, [$value->week_number => $value->not_ready]);
            array_push($wrap_up_raw, [$value->week_number => $value->wrap_up]);
            array_push($mtdg_raw, [$value->week_number =>  $value->mtdg]);
        }

        $in_call = $this->averagePerWeekly($this->mergeSameKey($in_call_raw));
        $ready = $this->averagePerWeekly($this->mergeSameKey($ready_raw));
        $not_ready = $this->averagePerWeekly($this->mergeSameKey($not_ready_raw));
        $wrap_up = $this->averagePerWeekly($this->mergeSameKey($wrap_up_raw));
        $loan_amount = $this->sumOfTheSameKey($this->mergeSameKey($loan_amount_raw));
        $real_loan = $this->getLoanAmmount($loan_amount);
        $scg = $this->removeDuplicateData($this->mergeSameKey($scg_raw));
        $mtdg = $this->removeDuplicateData($this->mergeSameKey($mtdg_raw));
        $fix_scg = $this->getSCG($scg);
        $fix_mtdg = $this->getMTDG($mtdg);
        $last_scg = $fix_scg[count($week)-1]*4;
        $last_mtdg = $fix_mtdg[count($week)-1]*4;
        // if ($last_scg[0] < array_sum($loan_amount)) {
        //     $loan_amount_ave = 100;
        // } else {
        //     $loan_amount_ave = (array_sum($loan_amount)/$last_scg[0])*100;
        // }
        if ($last_mtdg[0] < array_sum($loan_amount)) {
            $loan_amount_ave = 100;
        } else {
            $loan_amount_ave = (array_sum($loan_amount)/$last_mtdg[0])*100;
        }
        $loan_amount_score = $this->getLoanAmountScore($loan_amount_ave);
        $supervisor_check = $supervisor_data ?
        $supervisor_data->last_name. ", ".$supervisor_data->first_name : "No Supervisor in VPS record";

        $attendance_reliability = DashboardRaw::where('employee_no', $employee_no)
            ->where('update_status', '!=', 1)
            ->whereMonth('report_date', $month)
            ->whereIn('week_number', $week)
            ->orderBy('report_date', 'DECS')
            ->get();
        $grouped = $attendance_reliability->groupBy('week_number');
        $attendance = [];
        for ($i=1; $i < 5; $i++) {
            if ($grouped['Week '.$i] === null) {
                array_push($attendance, '');
            } else {
                array_push($attendance, $grouped['Week '.$i][0]['attendance_reliability']*100)."%";
            }
        }

        $average_attendance = array_sum($this->getAttendanceScoreArrayWeekly($attendance)) / count($attendance);
        $dateNow = Carbon::now();
        $dateSet = Carbon::createFromFormat('Y-m-d H:i:s', $dateNow, 'Europe/Stockholm');
        $dateSet->setTimezone('Asia/Manila');
        $dashboard_data = [
            'date' => $dateSet->format('Y-m-d'),
            'attendance_reliability' => $attendance,
            'attendance_reliability_ave' => $average_attendance,
            'name' => $user_data->last_name. ", " .$user_data->first_name,
            // 'scg' => $fix_scg,
            'mtdg' => $fix_mtdg,
            'days' => $dashboard_raw_data[0]->days ? $dashboard_raw_data[0]->days : '-',
            'employee_no' => $employee_no,
            'supervisor' => $supervisor_check,
            'department' => $user_data->department->name,
            'designation' => $user_data->designation->name,
            'tenureship' => $dashboard_raw_data[0]->tenureship,
            'coverage' => $dashboard_raw_data[0]->coverage,
            'portfolio' => $dashboard_raw_data[0]->portfolio,
            'in_call' => $this->average_per_week($in_call),
            // 'end_scg' => $last_scg,
            'end_mtdg' => $last_mtdg,
            'ready' => $this->average_per_week($ready),
            'not_ready' => $this->average_per_week($not_ready),
            'wrap_up' => $this->average_per_week($wrap_up),
            'in_call_ave' => $this->average($in_call),
            'ready_ave' => $this->average($ready),
            'not_ready_ave' => $this->average($not_ready),
            'wrap_up_ave' => $this->average($wrap_up),
            'in_call_tag' => $target['target_in_call'],
            'ready_tag' => $target['target_ready'],
            'not_ready_tag' => $target['target_not_ready'],
            'wrap_up_tag' => $target['target_wrap_up'],
            'loan_amount' => $real_loan,
            'loan_amount_score' => $loan_amount_score[0],
            'loan_amount_point' => $loan_amount_score[1],
            'loan_amount_ave' => number_format($loan_amount_ave, 2, '.', '')
        ];
        $dashboard_save_db = [
            'name' => $user_data->last_name. ", " .$user_data->first_name,
            // 'scg' => $fix_scg,
            'mtdg' => $fix_mtdg,
            'employee_no' => $employee_no,
            'days' => $dashboard_raw_data[0]->days ? $dashboard_raw_data[0]->days : '-',
            'supervisor' => $supervisor_check,
            'department' => $user_data->department->name,
            'designation' => $user_data->designation->name,
            'tenureship' => $dashboard_raw_data[0]->tenureship,
            'coverage' => $dashboard_raw_data[0]->coverage,
            'portfolio' => $dashboard_raw_data[0]->portfolio,
            'in_call' => serialize($in_call),
            // 'end_scg' => $last_scg[0],
            'end_mtdg' => $last_mtdg[0],
            'ready' => serialize($ready),
            'not_ready' => serialize($not_ready),
            'wrap_up' => serialize($wrap_up),
            'in_call_ave' => $this->average($in_call),
            'ready_ave' => $this->average($ready),
            'not_ready_ave' => $this->average($not_ready),
            'wrap_up_ave' => $this->average($wrap_up),
            'in_call_tag' => serialize($target['target_in_call']),
            'ready_tag' => serialize($target['target_ready']),
            'not_ready_tag' => serialize($target['target_not_ready']),
            'wrap_up_tag' => serialize($target['target_wrap_up']),
            'loan_amount' => serialize($real_loan),
            'loan_amount_score' => $loan_amount_score[0],
            'loan_amount_point' => $loan_amount_score[1],
            'loan_amount_ave' => number_format($loan_amount_ave, 2, '.', ''),
            'attendance_reliability' => $attendance,
            'attendance_reliability_ave' => $average_attendance,
            'weekly' => 1,
            'monthly' => 0,
            'created_by' => auth()->user()->first_name . " " . auth()->user()->last_name,
            'date_created' => $dateSet->format('Y-m-d h:m:s'),
        ];
        MonthlyPerformance::create($dashboard_save_db);
        $filename = "MPB_". str_replace(' ', '_', $user_data->last_name)
                . "_". str_replace(' ', '_', $user_data->first_name)
                . "_" . $employee_no;

        $pdf = PDF::loadView('pdf.generate.coaching_log.createWeeklyForm', $dashboard_data);

        return $pdf->download($filename.'.pdf');
    }

    public function coachingForm($id)
    {
        $coaching_form = CoachingLog::with([
            'kpi.loan_amount',
            'kpi.coaching_log',
            'kpi.qa_compliance',
            'kpi.qa_score',
            'kpi.attendance_reliability',
            'kpi.knowledge',
            'kpi.correction',
            'image_attachments'
        ])->where('id', $id)->first();
        $employee_details = User::where('employee_no', $coaching_form->employee_no)
        ->with(
            'department',
            'designation',
            'office_location',
            'employee_supervisor'
        )->first();
        $supervisor = User::where('employee_no', $employee_details->employee_supervisor->teamlead_employee_no)->first();
        $dateNow = new Carbon($coaching_form->date_created);
        $dateSet = Carbon::createFromFormat('Y-m-d H:i:s', $dateNow, 'Europe/Stockholm');
        $dateSet->setTimezone('Asia/Manila');

        $offense_id = [$coaching_form->offense_id];
        if (Str::contains($coaching_form->offense_id, '[') == 1) {
            $offense_id = explode(",", substr(str_replace( '"', '',$coaching_form->offense_id), 1, -1));
        }
        $offense = Offense::whereIn('id', $offense_id)->with('category', 'gravity')->get();
        $offense_array = [];
        foreach ($offense as $value) {
            $data_to_push = [
                'offense' => $value->offense,
                'category' => $value->category->name,
                'gravity' => $value->gravity->gravity
            ];
            array_push($offense_array, $data_to_push);
        }
        $coaching_data = [
            'date' => $dateSet->format('Y-m-d'),
            'name' => $employee_details->first_name." ".$employee_details->last_name,
            'position' => $employee_details->designation->name,
            'employee_no' => $coaching_form->employee_no,
            'department' => $employee_details->department->name,
            'supervisor' => $supervisor->first_name." ".$supervisor->last_name,
            'findings' => $coaching_form->findings,
            'point_of_disscussion' => $coaching_form->point_of_disscussion,
            'action_plans' => $coaching_form->action_plans,
            'agents_commitment' => $coaching_form->agents_commitment,
            'supervisors_commitment' => $coaching_form->supervisors_commitment,
            'offense' => $offense_array,
            'others' => $coaching_form->others,
            'performance_review' => $coaching_form->performance_review,
            'kpi' => $coaching_form->kpi,
            'cl_number' => $coaching_form->cl_number,
            'form_type' => $coaching_form->form_type,
        ];

        if ($coaching_form->form_type) {
            $added_form = $this->added_form($coaching_form->form_type, $coaching_form->cl_number);
            $coaching_data['form_added'] = $added_form;
        }

        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false) {
            $office = Office::where('site', 'like', '%square%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        }
        if ($coaching_form->image_attachments) {
            $coaching_data['overHeight'] = $this->checkHeight($coaching_form->image_attachments);
            $coaching_data['overWidth'] = $this->checkWidth($coaching_form->image_attachments);
            $coaching_data['file_image'] = $this->imageSetup($coaching_form->image_attachments);
        }

        for ($s1 = 1; $s1<=5; $s1++) {
            $stage_1_indicator = KeyPerformanceIndicator::findOrFail($s1);
            $coaching_data['stage_1_fifth_indicator' . $s1] = $stage_1_indicator->fifth_indicator;
            $coaching_data['stage_1_fourth_indicator' . $s1] = $stage_1_indicator->fourth_indicator;
            $coaching_data['stage_1_third_indicator' . $s1] = $stage_1_indicator->third_indicator;
            $coaching_data['stage_1_second_indicator' . $s1] = $stage_1_indicator->second_indicator;
            $coaching_data['stage_1_first_indicator' . $s1] = $stage_1_indicator->first_indicator;
        }

        for ($s2 = 6; $s2<=10; $s2++) {
            $stage_2_indicator = KeyPerformanceIndicator::findOrFail($s2);
            $coaching_data['stage_2_fifth_indicator' . $s2] = $stage_2_indicator->fifth_indicator;
            $coaching_data['stage_2_fourth_indicator' . $s2] = $stage_2_indicator->fourth_indicator;
            $coaching_data['stage_2_third_indicator' . $s2] = $stage_2_indicator->third_indicator;
            $coaching_data['stage_2_second_indicator' . $s2] = $stage_2_indicator->second_indicator;
            $coaching_data['stage_2_first_indicator' . $s2] = $stage_2_indicator->first_indicator;
        }

        $filename = "CL_". str_replace(' ', '_', $employee_details->last_name)
                . "_". str_replace(' ', '_', $employee_details->first_name)
                . "_" . $employee_details->employee_no;
        // dd($coaching_data);
        $pdf = PDF::loadView('pdf.generate.coaching_log.createCoachingForm1', $coaching_data);
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->download($filename.'.pdf');
    }

    private function added_form($form_type, $cl_number)
    {
        if ($form_type == 2) {
            $added_form = AdminForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 3) {
            $added_form = HrisForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 4) {
            $added_form = CNBForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 5) {
            $added_form = FinalPayForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 6) {
            $added_form = ManagerRatingSiteLeadForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 7) {
            $added_form = OnboardingForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 8) {
            $added_form = PayrollForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 9) {
            $added_form = RecruitmentForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 10) {
            $added_form = SelfRatingHrbpForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 11) {
            $added_form = SelfRatingHrbpSiteLeadForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 12) {
            $added_form = SourcingForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 13) {
            $added_form = SupervisorRecruitmentForms::where('cl_number', $cl_number)->first();
        } elseif ($form_type == 14) {
            $added_form = SupervisorHrbpForms::where('cl_number', $cl_number)->first();
        }

        return $added_form;
    }

    private function checkHeight($data)
    {
        $datas = str_replace('[', '', $data['attachments']);
        $data1 = str_replace(']', '', $datas);
        $data2 = str_replace('\\', '', $data1);
        $supported_image = [
            'jpg',
            'jpeg',
            'png'
        ];
        $return_array = [];
        foreach (explode(',', $data2) as $info) {
            $datas = str_replace('"', '', $info);

            $file = "attachments/".$datas;
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

    private function checkWidth($data)
    {
        $datas = str_replace('[', '', $data['attachments']);
        $data1 = str_replace(']', '', $datas);
        $data2 = str_replace('\\', '', $data1);
        $supported_image = [
            'jpg',
            'jpeg',
            'png'
        ];
        $return_array = [];
        foreach (explode(',', $data2) as $info) {
            $datas = str_replace('"', '', $info);

            $file = "attachments/".$datas;
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

    private function imageSetup($image_attachment)
    {

        $datas = str_replace('[', '', $image_attachment['attachments']);
        $data1 = str_replace(']', '', $datas);
        $data2 = str_replace('\\', '', $data1);
        $supported_image = [
            'jpg',
            'jpeg',
            'png'
        ];
        $array_file = [];
        foreach (explode(',', $data2) as $info) {
            $datas = str_replace('"', '', $info);
            $file = "attachments/".$datas;
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

    public function coachingPerformanceForm($id)
    {
        $coaching_form = CoachingLog::with([
            'performance.week_one',
            'performance.week_two',
            'performance.week_three',
            'performance.week_four',
            'performance.week_five',
            'performance.weight',
            'performance.current_score',
            'performance.rating_right',
            'performance.rating_left',
        ])->where('id', $id)->first();
        $employee_details = User::where('employee_no', $coaching_form->employee_no)
        ->with(
            'department',
            'designation',
            'office_location',
            'employee_supervisor'
        )->first();
        $supervisor = User::where('employee_no', $employee_details->employee_supervisor->teamlead_employee_no)->first();
        $week1 = $coaching_form->performance->week_one;
        $week2 = $coaching_form->performance->week_two;
        $week3 = $coaching_form->performance->week_three;
        $week4 = $coaching_form->performance->week_four;
        $week5 = $coaching_form->performance->week_five;
        $weight = $coaching_form->performance->weight;
        $current_score = $coaching_form->performance->current_score;
        $rating_right = $coaching_form->performance->rating_right;
        $rating_left = $coaching_form->performance->rating_left;
        $coaching_data = [
            'cl_number' => $coaching_form->cl_number,
            'date' => Carbon::parse($coaching_form->created_at)->format('Y-m-d'),
            'name' => $employee_details->first_name." ".$employee_details->last_name,
            'position' => $employee_details->designation->name,
            'employee_no' => $coaching_form->employee_no,
            'department' => $employee_details->department->name,
            'designation' => $employee_details->designation->name,
            'supervisor' => $supervisor->first_name." ".$supervisor->last_name,
            'identified_behavior' => $coaching_form->performance->identified_behavior,
            'root_cause' => $coaching_form->performance->root_cause,
            'action_plans' => $coaching_form->performance->action_plans,
            'base_performance' => $coaching_form->performance->base_performance,
            'goal' => $coaching_form->performance->goal,
            'extended_action_plan' => $coaching_form->performance->extended_action_plan,
            'justification' => $coaching_form->performance->justification,
            'additional_feedback' => $coaching_form->performance->additional_feedback,
            'week1_target_in_call' => $week1 ? $week1->target_in_call : '',
            'week1_target_ready' => $week1 ? $week1->target_ready : '',
            'week1_target_wrap_up' => $week1 ? $week1->target_wrap_up : '',
            'week1_target_not_ready' => $week1 ? $week1->target_not_ready : '',
            'week1_target_loan_origination' => $week1 ? $week1->target_loan_origination : '',
            'week1_target_qa_scores' => $week1 ? $week1->target_qa_scores : '',
            'week1_target_compliance' => $week1 ? $week1->target_compliance : '',
            'week1_target_attendance' => $week1 ? $week1->target_attendance : '',
            'week1_actual_in_call' => $week1 ? $week1->actual_in_call : '',
            'week1_actual_ready' => $week1 ? $week1->actual_ready : '',
            'week1_actual_wrap_up' => $week1 ? $week1->actual_wrap_up : '',
            'week1_actual_not_ready' => $week1 ? $week1->actual_not_ready : '',
            'week1_actual_loan_origination' => $week1 ? $week1->actual_loan_origination : '',
            'week1_actual_qa_scores' => $week1 ? $week1->actual_qa_scores : '',
            'week1_actual_compliance' => $week1 ? $week1->actual_compliance : '',
            'week1_actual_attendance' => $week1 ? $week1->actual_attendance : '',
            'week2_target_in_call' => $week2 ? $week2->target_in_call : '',
            'week2_target_ready' => $week2 ? $week2->target_ready : '',
            'week2_target_wrap_up' => $week2 ? $week2->target_wrap_up : '',
            'week2_target_not_ready' => $week2 ? $week2->target_not_ready : '',
            'week2_target_loan_origination' => $week2 ? $week2->target_loan_origination : '',
            'week2_target_qa_scores' => $week2 ? $week2->target_qa_scores : '',
            'week2_target_compliance' => $week2 ? $week2->target_compliance : '',
            'week2_target_attendance' => $week2 ? $week2->target_attendance : '',
            'week2_actual_in_call' => $week2 ? $week2->actual_in_call : '',
            'week2_actual_ready' => $week2 ? $week2->actual_ready : '',
            'week2_actual_wrap_up' => $week2 ? $week2->actual_wrap_up : '',
            'week2_actual_not_ready' => $week2 ? $week2->actual_not_ready : '',
            'week2_actual_loan_origination' => $week2 ? $week2->actual_loan_origination : '',
            'week2_actual_qa_scores' => $week2 ? $week2->actual_qa_scores : '',
            'week2_actual_compliance' => $week2 ? $week2->actual_compliance : '',
            'week2_actual_attendance' => $week2 ? $week2->actual_attendance : '',
            'week3_target_in_call' => $week3 ? $week3->target_in_call : '',
            'week3_target_ready' => $week3 ? $week3->target_ready : '',
            'week3_target_wrap_up' => $week3 ? $week3->target_wrap_up : '',
            'week3_target_not_ready' => $week3 ? $week3->target_not_ready : '',
            'week3_target_loan_origination' => $week3 ? $week3->target_loan_origination : '',
            'week3_target_qa_scores' => $week3 ? $week3->target_qa_scores : '',
            'week3_target_compliance' => $week3 ? $week3->target_compliance : '',
            'week3_target_attendance' => $week3 ? $week3->target_attendance : '',
            'week3_actual_in_call' => $week3 ? $week3->actual_in_call : '',
            'week3_actual_ready' => $week3 ? $week3->actual_ready : '',
            'week3_actual_wrap_up' => $week3 ? $week3->actual_wrap_up : '',
            'week3_actual_not_ready' => $week3 ? $week3->actual_not_ready : '',
            'week3_actual_loan_origination' => $week3 ? $week3->actual_loan_origination : '',
            'week3_actual_qa_scores' => $week3 ? $week3->actual_qa_scores : '',
            'week3_actual_compliance' => $week3 ? $week3->actual_compliance : '',
            'week3_actual_attendance' => $week3 ? $week3->actual_attendance : '',
            'week4_target_in_call' => $week4 ? $week4->target_in_call : '',
            'week4_target_ready' => $week4 ? $week4->target_ready : '',
            'week4_target_wrap_up' => $week4 ? $week4->target_wrap_up : '',
            'week4_target_not_ready' => $week4 ? $week4->target_not_ready : '',
            'week4_target_loan_origination' => $week4 ? $week4->target_loan_origination : '',
            'week4_target_qa_scores' => $week4 ? $week4->target_qa_scores : '',
            'week4_target_compliance' => $week4 ? $week4->target_compliance : '',
            'week4_target_attendance' => $week4 ? $week4->target_attendance : '',
            'week4_actual_in_call' => $week4 ? $week4->actual_in_call : '',
            'week4_actual_ready' => $week4 ? $week4->actual_ready : '',
            'week4_actual_wrap_up' => $week4 ? $week4->actual_wrap_up : '',
            'week4_actual_not_ready' => $week4 ? $week4->actual_not_ready : '',
            'week4_actual_loan_origination' => $week4 ? $week4->actual_loan_origination : '',
            'week4_actual_qa_scores' => $week4 ? $week4->actual_qa_scores : '',
            'week4_actual_compliance' => $week4 ? $week4->actual_compliance : '',
            'week4_actual_attendance' => $week4 ? $week4->actual_attendance : '',
            'week5' => $week5 ? $week5 : '',
            'week5_target_in_call' => $week5 ? $week5->target_in_call : '',
            'week5_target_ready' => $week5 ? $week5->target_ready : '',
            'week5_target_wrap_up' => $week5 ? $week5->target_wrap_up : '',
            'week5_target_not_ready' => $week5 ? $week5->target_not_ready : '',
            'week5_target_loan_origination' => $week5 ? $week5->target_loan_origination : '',
            'week5_target_qa_scores' => $week5 ? $week5->target_qa_scores : '',
            'week5_target_compliance' => $week5 ? $week5->target_compliance : '',
            'week5_target_attendance' => $week5 ? $week5->target_attendance : '',
            'week5_actual_in_call' => $week5 ? $week5->actual_in_call : '',
            'week5_actual_ready' => $week5 ? $week5->actual_ready : '',
            'week5_actual_wrap_up' => $week5 ? $week5->actual_wrap_up : '',
            'week5_actual_not_ready' => $week5 ? $week5->actual_not_ready : '',
            'week5_actual_loan_origination' => $week5 ? $week5->actual_loan_origination : '',
            'week5_actual_qa_scores' => $week5 ? $week5->actual_qa_scores : '',
            'week5_actual_compliance' => $week5 ? $week5->actual_compliance : '',
            'week5_actual_attendance' => $week5 ? $week5->actual_attendance : '',
            'weight_loan_amount' => $weight ? $weight->loan_amount : '',
            'weight_qa_score' => $weight ? $weight->qa_score : '',
            'weight_correction' => $weight ? $weight->correction : '',
            'weight_compliance' => $weight ? $weight->compliance : '',
            'weight_attendance_reliability' => $weight ? $weight->attendance_reliability : '',
            'weight_total' => $weight ? $weight->total : '',
            'current_score_loan_amount' => $current_score ? $current_score->loan_amount : '',
            'current_score_qa_score' => $current_score ? $current_score->qa_score : '',
            'current_score_correction' => $current_score ? $current_score->correction : '',
            'current_score_compliance' => $current_score ? $current_score->compliance : '',
            'current_score_attendance_reliability' => $current_score ? $current_score->attendance_reliability : '',
            'rating_right_loan_amount' => $rating_right ? $rating_right->loan_amount : '',
            'rating_right_qa_score' => $rating_right ? $rating_right->qa_score : '',
            'rating_right_correction' => $rating_right ? $rating_right->correction : '',
            'rating_right_compliance' => $rating_right ? $rating_right->compliance : '',
            'rating_right_attendance_reliability' => $rating_right ? $rating_right->attendance_reliability : '',
            'rating_right_total' => $rating_right ? $rating_right->total : '',
            'rating_left_loan_amount' => $rating_left ? $rating_left->loan_amount : '',
            'rating_left_qa_score' => $rating_left ? $rating_left->qa_score : '',
            'rating_left_correction' => $rating_left ? $rating_left->correction : '',
            'rating_left_compliance' => $rating_left ? $rating_left->compliance : '',
            'rating_left_attendance_reliability' => $rating_left ? $rating_left->attendance_reliability : '',
            'rating_left_total' => $rating_left ? $rating_left->total : '',
        ];

        if (Str::contains(strtolower(auth()->user()->office_location->name), 'square') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bgc') !== false) {
            $office = Office::where('site', 'like', '%square%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'market') !== false) {
            $office = Office::where('site', 'like', '%MARKET%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'bacolod') !== false ||
            Str::contains(strtolower(auth()->user()->office_location->name), 'bcd') !== false
            ) {
            $office = Office::where('site', 'like', '%BACOLOD%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        } elseif (Str::contains(strtolower(auth()->user()->office_location->name), 'alabang') !== false) {
            $office = Office::where('site', 'like', '%ALABANG%')->first();
            $coaching_data['work_location'] = $office->complete_address;
        }
        $filename = "CL_". str_replace(' ', '_', $employee_details->last_name)
                . "_". str_replace(' ', '_', $employee_details->first_name)
                . "_" . $employee_details->employee_no;
        $pdf = PDF::loadView('pdf.generate.coaching_log.createWeeklyForm', $coaching_data);
        $pdf->getDomPDF()->set_option("enable_php", true);
        
        return $pdf->download($filename.'.pdf');
    }

    public function acknowledge($id)
    {
        $dateSet = Carbon::now();
        $dateSet->setTimezone('Asia/Manila');

        $coaching_form = CoachingLog::where('id', $id)->update([
            'acknowledge_date' => $dateSet->format('Y-m-d'),
        ]);

        if ($coaching_form) {
            $coaching = CoachingLog::where('id', $id)->first();
            $user = User::where('id', $coaching->created_by_employee_id)->first();
            EmailCoachingLog::dispatch(
                $coaching->cl_number,
                $user->email_address,
                $coaching->created_at,
                'coaching_acknowledge'
            );
        }
        if ($coaching_form) {
            return response()->json(['isValid'=>true]);
        } else {
            return response()->json(['isValid'=>false]);
        }
    }

    public function convertString(String $str)
    {
        $thousand_raw = str_replace("$", "", $str);
        $thousand = (int)str_replace(",", "", $thousand_raw);

        return $thousand;
    }

    public function checkDashboardColumnLess($dashboard_uploaded)
    {
        $dashboard_array = ['employee_id', 'employee_name', 'designation', 'priority', 'tenureship', 'portfolio',
            'site_location', 'days', 'coverage', 'scg', 'mtdg', 'team', 'cluster', 'loan_amount', 'attendance',
            'in_call', 'ready', 'wrap_up', 'not_ready', 'coveragetotal_loan_amount', 'mtdtotal_loan_amount',
            'attendancereliability', 'major', 'minor', 'reportdate', 'week_count', 'return_week'
        ];

        $stats = array_diff($dashboard_array, array_keys($dashboard_uploaded->toArray()));

        return $stats;
    }

    public function importExcel(Request $request)
    {
        $path = $request->file('attachments')[0]->getRealPath();
        try {
            Excel::filter('chunk')->load($path)->chunk(200, function ($results) {
                foreach ($results as $key => $value) {
                    $validator = ValidatorController::disputeDashboardValidator($value);
                    if (count($value) > 27) {
                        throw new Exception('Excess column please check');
                    } elseif (!$validator->fails()) {
                        $date = strtotime($value->reportdate);
                        $portfolio = EfficiencyGoal::where('portfolio_name', $value->portfolio)
                            ->where('priority', $value->priority)
                            ->first();
                        $user_check = User::where('employee_no', $value->employee_id)->first();
                        if (!empty($portfolio) && $user_check) {
                            DashboardRaw::firstOrCreate(
                                [
                                'employee_no' => $value->employee_id,
                                'tenureship' => $value->tenureship,
                                'portfolio' => $value->portfolio,
                                'scg' => $this->convertString($value->scg),
                                'team' => $value->team,
                                'loan_amount' => $value->loan_amount,
                                'attendance' => $value->attendance,
                                'in_call' => $value->in_call,
                                'ready' => $value->ready,
                                'wrap_up' => $value->wrap_up,
                                'not_ready' => $value->not_ready,
                                'coverage_total_loan_amount' => $this->convertString($value->coveragetotal_loan_amount),
                                'attendance_reliability' => $value->attendancereliability,
                                'report_date' => date('Y-m-d', $date),
                                'week_number' => $value->week_count,
                                'update_status' => 0,
                                'site_location' => $value->site_location,
                                'minor' => $value->minor,
                                'major' => $value->major,
                                'running_week' => $value->running_week,
                                'cluster' => $value->cluster,
                                'mtd_total_loan_amount' => $this->convertString($value->mtdtotal_loan_amount),
                                'mtdg' => $this->convertString($value->mtdg),
                                'days' => $value->days,
                                'attendance' => $value->attendance,
                                'coverage' => $value->coverage
                                ],
                                [
                                'employee_no' => $value->employee_id,
                                'tenureship' => $value->tenureship,
                                'portfolio' => $value->portfolio,
                                'coverage' => $value->coverage,
                                'scg' => $this->convertString($value->scg),
                                /* 'site_and_portfolio' => $value->site_and_portfolio, */
                                'team' => $value->team,
                                'loan_amount' => $value->loan_amount,
                                'attendance' => $value->attendance,
                                'in_call' => $value->in_call,
                                'ready' => $value->ready,
                                'wrap_up' => $value->wrap_up,
                                'not_ready' => $value->not_ready,
                                'in_call_target' => $portfolio->in_call,
                                'ready_target' => $portfolio->ready,
                                'wrap_up_target' => $portfolio->wrap_up,
                                'not_ready_target' => $portfolio->not_ready,
                                'days' => $value->days,
                                /* change header name */
                                'coverage_total_loan_amount' => $this->convertString($value->coveragetotal_loan_amount),
                                'attendance_reliability' => $value->attendancereliability,
                                'report_date' => date('Y-m-d', $date),
                                'week_number' => $value->week_count,
                                /* end of change header name */
                                'update_status' => 0,
                                /* add fields */
                                'site_location' => $value->site_location,
                                'minor' => $value->minor,
                                'major' => $value->major,
                                'running_week' => $value->running_week,
                                'cluster' => $value->cluster,
                                'mtd_total_loan_amount' => $this->convertString($value->mtdtotal_loan_amount),
                                'mtdg' => $this->convertString($value->mtdg),
                                'attendance' => $value->attendance
                                ]
                            );
                        } elseif (!$user_check) {
                            $row_number = $key+1;
                            throw new Exception("Not Registered Employee found in row ".$row_number);
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

    public function importImage($cl_no, Request $request)
    {
        if ($request->file('attachments_image')) {
            $storage = 'public/attachments/coaching_log/'
                        . $cl_no . '/' . Carbon::today()->format('Y-m-d')
                        . '/';

            $uploaded = [];
            foreach ($request->file('attachments_image') as $file) {
                $filename = $file->getClientOriginalName();
                $data = Storage::disk('local')->putFileAs($storage, new File($file), $filename, 'public');

                $uploaded[] = 'coaching_log/' . $cl_no . '/' . Carbon::today()->format('Y-m-d') . '/' . $filename;
            }

            $contents = Storage::disk('local')->files($storage);

            if ($uploaded) {
                $attach = new ImageAttachment;
                $attach->cl_number = $cl_no;
                $attach->attachments = json_encode($uploaded);

                if ($attach->save()) {
                    Coachinglog::where('cl_number', $cl_no)
                    ->update([
                        'image_attachment_id' => $attach->id,
                    ]);
                }
            }

            return response(['uploaded' => $uploaded ]);
        }
    }

    public function importKPI($employee_no, Request $request)
    {
        $emp_no = $employee_no;
        if ($request->file('attachments')[0]) {
            $path = $request->file('attachments')[0]->getRealPath();
            try {
                Excel::filter('chunk')->load($path)->chunk(200, function ($results) use(&$employee_no) {
                    $attach = [];
                    if (count($results) == 1) {
                        foreach ($results as $key => $value) {
                            if (count($value) > 25 && (
                                strtolower($value->stage_of_kpi_score_card) == 'stage 2'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 3'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 4'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 5'
                                || strtolower($value->stage_of_kpi_score_card) == 'regular'
                            )) {
                                throw new Exception('Excess column please check');
                            } elseif (count($value) > 28 && strtolower($value->stage_of_kpi_score_card) == 'stage 1') {
                                throw new Exception('Excess column please check');
                            } elseif (count($value) < 25 && (
                                strtolower($value->stage_of_kpi_score_card) == 'stage 2'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 3'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 4'
                                || strtolower($value->stage_of_kpi_score_card) == 'stage 5'
                                || strtolower($value->stage_of_kpi_score_card) == 'regular'
                            )) {
                                throw new Exception('Less column please check');
                            } elseif (count($value) < 28 && strtolower($value->stage_of_kpi_score_card) == 'stage 1') {
                                throw new Exception('Less column please check');
                            } elseif ($key == 0) {
                                $user_check = User::where('employee_no', $value->employee_id)->first();
                                if ($user_check && $user_check->employee_no == $employee_no) {
                                    if (strtolower($value->stage_of_kpi_score_card) == 'stage 2'
                                        || strtolower($value->stage_of_kpi_score_card) == 'stage 3'
                                        || strtolower($value->stage_of_kpi_score_card) == 'stage 4'
                                        || strtolower($value->stage_of_kpi_score_card) == 'stage 5'
                                        || strtolower($value->stage_of_kpi_score_card) == 'regular' ) {
                                        $loan_amount_array_data = [
                                            'target' => (string)$value->loan_amount_target,
                                            'rating' => (string)$value->loan_amount_rating,
                                            'computation' => (string)$value->loan_amount_computation,
                                            'comment' => (string)$value->loan_amount_comment
                                        ];
                                        $qa_compliance_array_data = [
                                            'target' => (string)$value->qa_compliance_target,
                                            'rating' => (string)$value->qa_compliance_rating,
                                            'computation' => (string)$value->qa_compliance_computation,
                                            'comment' => (string)$value->qa_compliance_comment
                                        ];
                                        $qa_score_array_data = [
                                            'target' => (string)$value->qa_score_target,
                                            'rating' => (string)$value->qa_score_rating,
                                            'computation' => (string)$value->qa_score_computation,
                                            'comment' => (string)$value->qa_score_comment
                                        ];
                                        $correction_array_data = [
                                            'target' => (string)$value->correction_target,
                                            'rating' => (string)$value->correction_rating,
                                            'computation' => (string)$value->correction_computation,
                                            'comment' => (string)$value->correction_comment
                                        ];
                                        $attendance_reliability_array_data = [
                                            'target' => (string)$value->attendance_reliability_target,
                                            'rating' => (string)$value->attendance_reliability_rating,
                                            'computation' => (string)$value->attendance_reliability_computation,
                                            'comment' => (string)$value->attendance_reliability_comment
                                        ];
                                        $loan_amount = LoanAmountKpi::create($loan_amount_array_data);
                                        $qa_compliance = QaComplianceKpi::create($qa_compliance_array_data);
                                        $qa_score = QaScoreKpi::create($qa_score_array_data);
                                        $correction = CorrectionKpi::create($correction_array_data);
                                        $attendance_reliability = AttendanceReliabilityKpi::create($attendance_reliability_array_data);
                                        $score_card = ScoreCardKpi::create([
                                            'stage' => strtolower($value->stage_of_kpi_score_card),
                                            'loan_amount_kpi_id' => $loan_amount->id,
                                            'qa_compliance_kpi_id' => $qa_compliance->id,
                                            'qa_score_kpi_id' => $qa_score->id,
                                            'correction_kpi_id' => $correction->id,
                                            'attendance_reliability_kpi_id' => $attendance_reliability->id,
                                            'weakness' => $value->weakness,
                                            'strengths' => $value->strengths,
                                            'action_plans' => $value->action_plans
                                        ]);
                                        throw new Exception('score_card_id:'.$score_card->id);
                                    } elseif (strtolower($value->stage_of_kpi_score_card) == 'stage 1') {
                                        $rating_array = [
                                            $value->loan_amount_rating_eom,
                                            $value->loan_amount_rating_w2,
                                            $value->loan_amount_rating_w3,
                                            $value->loan_amount_rating_w4
                                        ];
                                        $loan_amount_array_data = [
                                            'target' => (string)$value->loan_amount_target,
                                            'rating' => serialize($rating_array),
                                            'computation' => (string)$value->loan_amount_computation,
                                            'comment' => (string)$value->loan_amount_comment
                                        ];
                                        $knowledge_array_data = [
                                            'target' => (string)$value->knowledge_target,
                                            'rating' => (string)$value->knowledge_rating,
                                            'computation' => (string)$value->knowledge_computation,
                                            'comment' => (string)$value->knowledge_comment
                                        ];
                                        $qa_score_array_data = [
                                            'target' => (string)$value->qa_score_target,
                                            'rating' => (string)$value->qa_score_rating,
                                            'computation' => (string)$value->qa_score_computation,
                                            'comment' => (string)$value->qa_score_comment
                                        ];
                                        $coaching_log_array_data = [
                                            'target' => (string)$value->coaching_log_target,
                                            'rating' => (string)$value->coaching_log_rating,
                                            'computation' => (string)$value->coaching_log_computation,
                                            'comment' => (string)$value->coaching_log_comment
                                        ];
                                        $attendance_reliability_array_data = [
                                            'target' => (string)$value->attendance_reliability_target,
                                            'rating' => (string)$value->attendance_reliability_rating,
                                            'computation' => (string)$value->attendance_reliability_computation,
                                            'comment' => (string)$value->attendance_reliability_comment
                                        ];
                                        $loan_amount = LoanAmountKpi::create($loan_amount_array_data);
                                        $knowledge = KnowledgeKpi::create($knowledge_array_data);
                                        $qa_score = QaScoreKpi::create($qa_score_array_data);
                                        $coaching_log = CoachingLogKpi::create($coaching_log_array_data);
                                        $attendance_reliability = AttendanceReliabilityKpi::create($attendance_reliability_array_data);
                                        $score_card = ScoreCardKpi::create([
                                            'stage' => strtolower($value->stage_of_kpi_score_card),
                                            'loan_amount_kpi_id' => $loan_amount->id,
                                            'knowledge_kpi_id' => $knowledge->id,
                                            'qa_score_kpi_id' => $qa_score->id,
                                            'coaching_log_kpi_id' => $coaching_log->id,
                                            'attendance_reliability_kpi_id' => $attendance_reliability->id,
                                            'weakness' => $value->weakness,
                                            'strengths' => $value->strengths,
                                            'action_plans' => $value->action_plans
                                        ]);
                                        throw new Exception('score_card_id:'.$score_card->id);
                                    } else {
                                        throw new Exception('No Stage');
                                    }
                                } elseif ($user_check->employee_no != $employee_no) {
                                    throw new Exception("Employee selected does not match to the csv file ");
                                } elseif (!$user_check) {
                                    $row_number = $key+1;
                                    throw new Exception("Not Registered Employee found in row ".$row_number);
                                }
                            }
                        }
                    } else {
                        throw new Exception("Error, Please One employee record per upload Csv File");
                    }
                });
            } catch (Exception $exception) {
                if(strpos($exception->getMessage(), 'score_card_id') !== false){
                    $score_card_id = explode(':',$exception->getMessage());
                    return response()->json(['isvalid'=>true,'score_card_id'=>$score_card_id[1]]);
                } else{
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
            }

            return response()->json(['isvalid'=>true]);
        } else {
            return response()->json(['isvalid'=>false]);
        }
    }

    public function importExcelDispute(Request $request)
    {
        $path = $request->file('attachments')[0]->getRealPath();
        try {
            Excel::filter('chunk')->load($path)->chunk(200, function ($results) {
                foreach ($results as $key => $val) {
                    $validator = ValidatorController::disputeDashboardValidator($val);
                    if (count($val) > 27) {
                        throw new Exception('Excess column please check');
                    } elseif (!$validator->fails()) {
                        $date = strtotime($val->reportdate);
                        $portfolio = EfficiencyGoal::where('portfolio_name', $val->portfolio)
                            ->where('priority', $val->priority)
                            ->first();
                        $user_check = User::where('employee_no', $val->employee_id)->first();
                        if (!empty($portfolio) && $user_check) {
                            $data = DashboardRaw::where('report_date', date('Y-m-d', $date))
                            ->where('employee_no', (int)$val->employee_id)
                            ->where('update_status', 0)
                            ->update([
                                'update_status' => 1,
                                'update_date' => date('Y-m-d')
                            ]);
                            if ($data > 0) {
                                $dispute = DashboardRaw::where('report_date', date('Y-m-d', $date))
                                ->where('employee_no', $val->employee_id)->first();

                                DisputeDashboard::create([
                                    'employee_no' => $val->employee_id,
                                    'created_by' => auth()->user()->first_name . " " . auth()->user()->last_name,
                                    'date_updated' => date('Y-m-d h:m:s'),
                                    'dashboard_raw_id' => $dispute->id
                                ]);
                                DashboardRaw::create([
                                    'employee_no' => $val->employee_id,
                                    'tenureship' => $val->tenureship,
                                    'portfolio' => $val->portfolio,
                                    'coverage' => $val->coverage,
                                    'scg' => $this->convertString($val->scg),
                                    /* 'site_and_portfolio' => $val->site_and_portfolio, */
                                    'team' => $val->team,
                                    'loan_amount' => $val->loan_amount,
                                    'in_call' => $val->in_call,
                                    'ready' => $val->ready,
                                    'wrap_up' => $val->wrap_up,
                                    'not_ready' => $val->not_ready,
                                    'in_call_target' => $portfolio->in_call,
                                    'ready_target' => $portfolio->ready,
                                    'wrap_up_target' => $portfolio->wrap_up,
                                    'not_ready_target' => $portfolio->not_ready,
                                    'days' => $val->days,
                                    /* change header name */
                                    'coverage_total_loan_amount' => $this->convertString($val->coveragetotal_loan_amount),
                                    'attendance_reliability' => $val->attendancereliability,
                                    'report_date' => date('Y-m-d', $date),
                                    'week_number' => $val->week_count,
                                    /* end of change header name */
                                    'update_status' => 0,
                                    /* add fields */
                                    'site_location' => $val->site_location,
                                    'minor' => $val->minor,
                                    'major' => $val->major,
                                    'running_week' => $val->running_week,
                                    'cluster' => $val->cluster,
                                    'mtd_total_loan_amount' => $this->convertString($val->mtdtotal_loan_amount),
                                    'mtdg' => $this->convertString($val->mtdg),
                                    'attendance' => $val->attendance
                                ]);
                            }
                        } elseif (!$user_check) {
                            $row_number = $key+1;
                            throw new Exception("Not Registered Employee found in row ".$row_number);
                        }
                    } else {
                        throw new Exception($validator->messages(). " Error occured in row number ". ($key+1));
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

    public function downloadForm($id)
    {
        $form = MonthlyPerformance::where('id', $id)->first();
        $data = [];
        foreach ($form->attributes as $value) {
            array_push($data, $value);
        }
        $form->in_call = unserialize($form->in_call);
        $form->ready = unserialize($form->ready);
        $form->wrap_up = unserialize($form->wrap_up);
        $form->not_ready = unserialize($form->not_ready);
        $form->loan_amount = unserialize($form->loan_amount);
        $employee_details = User::where('employee_no', $coaching_form->employee_no)
        ->with('department', 'designation', 'office_location', 'employee_supervisor')
        ->first();
        $filename = "CL_". str_replace(' ', '_', $form->name)
                . "_" . $form->employee_no;

        if ($form->monthly === 1) {
            $pdf = PDF::loadView('pdf.generate.coaching_log.createMonthlyForm', $form);
        } elseif ($form->weekly === 1) {
            $pdf = PDF::loadView('pdf.generate.coaching_log.createWeeklyForm', $form);
        }

        return $pdf->download($filename.'.pdf');
    }

    public function exportCoachingLogReport()
    {
        ini_set('memory_limit', '4095M');
        $closed = CoachingLog::with([
            'user_employee',
            'hrbp_coaching.hrbp_user', 
            'hrbp_coaching.site_office', 
            'offense'
        ])
        ->orderBy('cl_number', 'desc')
        ->get();

        $data_return = [];
        foreach ($closed as $value) {

            if ($value->performance_id) {
                $value['performance'] = "Weekly Performance";
            } else {
                if ($value->form_type == "0" || $value->form_type == "1") {
                    $value['performance'] = "Monthly Performance";
                } else {
                    $value['performance'] = "Coaching Log";
                }
            }

            switch ($value->form_type) {
                case "0":
                    $value['form_type'] = "Stage One Form";
                    break;
                case "1":
                    $value['form_type'] = "Stage Two to Five Form";
                    break;
                case "2":
                    $value['form_type'] = "Admin Form";
                    break;
                case "3":
                    $value['form_type'] = "HRIS Form";
                    break;
                case "4":
                    $value['form_type'] = "C&B Form";
                    break;
                case "5":
                    $value['form_type'] = "Final Pay Form";
                    break;
                case "6":
                    $value['form_type'] = "Manager Rating HRBP Site Lead Form";
                    break;
                case "7":
                    $value['form_type'] = "Onboarding Form";
                    break;
                case "8":
                    $value['form_type'] = "Payroll Form";
                    break;
                case "9":
                    $value['form_type'] = "Recruitment Form";
                    break;
                case "10":
                    $value['form_type'] = "Self Rating HRBP Form";
                    break;
                case "11":
                    $value['form_type'] = "Self Rating HRBP Site Lead  Form";
                    break;
                case "12":
                    $value['form_type'] = "Sourcing Form";
                    break;
                case "13":
                    $value['form_type'] = "Recruitment Supervisor Rating Form<";
                    break;
                case "14":
                    $value['form_type'] = "Supervisor Rating HRBP Form";
                    break;
                case "15":
                    $value['form_type'] = "Training Form";
                    break;
                default:
                    $value['form_type'] = "Coaching Form";
            }

            if ($value->performance_id) {
                $value['form_type'] = "Weekly Form";
            }

            array_push($data_return, $value);
        }

        $data_to_collection = Collection::make($data_return);

        return $data_to_collection;
    }

    public function searchQuery($searchKey)
    {        
        ini_set('memory_limit', '4095M');

        $findEmployee = User::where('first_name', 'LIKE' , '%'.$searchKey.'%')
        ->orWhere('last_name', 'LIKE' , '%'.$searchKey.'%')
        ->first();

        $findEmpNo = $findEmployee->employee_no;
        if ($findEmpNo) {
            $findKey = $findEmpNo;
        } else {
            $findKey = $searchKey;
        }

        $coachings = CoachingLog::with([
            'user_employee.employee_supervisor.supervisor_assign',
            'performance.weight',
            'performance.current_score',
            'performance.rating_right',
            'performance.rating_left',
            'performance.week_one',
            'performance.week_two',
            'performance.week_three',
            'performance.week_four',
            'performance.week_five'
        ])
        ->where('employee_no', 'LIKE' , '%'.$findKey.'%')
        ->orWhere('cl_number', 'LIKE' , '%'.$searchKey.'%')
        ->orderBy('cl_number', 'desc')
        ->get();

        return response()->json($coachings);
    }

    public function keyPerformanceIndicator($id)
    {
        $indicator = KeyPerformanceIndicator::findOrFail($id);
        return response()->json($indicator);
    }

    public function keyPerformanceIndicatorUpdate(int $id, Request $request)
    {
        $indicator = KeyPerformanceIndicator::where('id', $id)
        ->update([
            'fifth_indicator' => $request->fifth_indicator,
            'fourth_indicator' => $request->fourth_indicator,
            'third_indicator' => $request->third_indicator,
            'second_indicator' => $request->second_indicator,
            'first_indicator' => $request->first_indicator
        ]);
        return $indicator;
    }
}
