<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class OnboardingForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'onboarding_forms';

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
        'completion_target',
        'completion_weight',
        'completion_rating_scale',
        'completion_final_score',
        'submission_target',
        'submission_weight',
        'submission_rating_scale',
        'submission_final_score',
        'bank_target',
        'bank_weight',
        'bank_rating_scale',
        'bank_final_score',
        'hmo_target',
        'hmo_weight',
        'hmo_rating_scale',
        'hmo_final_score',
        'escalation_target',
        'escalation_weight',
        'escalation_rating_scale',
        'escalation_final_score',
        'inssuance_target',
        'inssuance_weight',
        'inssuance_rating_scale',
        'inssuance_final_score',
        'submission_by_weekly_target',
        'submission_by_weekly_weight',
        'submission_by_weekly_rating_scale',
        'submission_by_weekly_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
