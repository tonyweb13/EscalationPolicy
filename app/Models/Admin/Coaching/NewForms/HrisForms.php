<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class HrisForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'hris_forms';

    protected $fillable = [
        'initiative_target',
        'initiative_weight',
        'initiative_rating_scale',
        'initiative_final_score',
        'work_ethics_target',
        'work_ethics_weight',
        'work_ethics_rating_scale',
        'work_ethics_final_score',
        'responsiveness_target',
        'responsiveness_weight',
        'responsiveness_rating_scale',
        'responsiveness_final_score',
        'attendance_reliability_target',
        'attendance_reliability_weight',
        'attendance_reliability_rating_scale',
        'attendance_reliability_final_score',
        'timely_and_accuraccy_of_on_boarding_target',
        'timely_and_accuraccy_of_on_boarding_weight',
        'timely_and_accuraccy_of_on_boarding_rating_scale',
        'timely_and_accuraccy_of_on_boarding_final_score',
        'timely_and_accuraccy_of_updating_in_masterfile_target',
        'timely_and_accuraccy_of_updating_in_masterfile_weight',
        'timely_and_accuraccy_of_updating_in_masterfile_rating_scale',
        'timely_and_accuraccy_of_updating_in_masterfile_final_score',
        'submission_of_weekly_reports_target',
        'submission_of_weekly_reports_weight',
        'submission_of_weekly_reports_rating_scale',
        'submission_of_weekly_reports_final_score',
        'final_pay_target',
        'final_pay_weight',
        'final_pay_rating_scale',
        'final_pay_final_score',
        'number_of_escalation_target',
        'number_of_escalation_weight',
        'number_of_escalation_rating_scale',
        'number_of_escalation_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
