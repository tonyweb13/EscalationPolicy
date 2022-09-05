<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class FinalPayForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'final_pay_forms';

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
        'accuracy_computation_target',
        'accuracy_computation_weight',
        'accuracy_computation_rating_scale',
        'accuracy_computation_final_score',
        'timely_target',
        'timely_weight',
        'timely_rating_scale',
        'timely_final_score',
        'submission_of_final_rating_target',
        'submission_of_final_rating_weight',
        'submission_of_final_rating_scale',
        'submission_of_final_rating_final_score',
        'number_target',
        'number_weight',
        'number_rating_scale',
        'number_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
