<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class SelfRatingHrbpForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'self_rating_hrbp_forms';

    protected $fillable = [
        'critical_thinking_target',
        'critical_thinking_weight',
        'critical_thinking_rating_scale',
        'critical_thinking_final_score',
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
        'data_target',
        'data_weight',
        'data_rating_scale',
        'data_final_score',
        'intervention_target',
        'intervention_weight',
        'intervention_rating_scale',
        'intervention_final_score',
        'cluster_target',
        'cluster_weight',
        'cluster_rating_scale',
        'cluster_final_score',
        'productivity_target',
        'productivity_weight',
        'productivity_rating_scale',
        'productivity_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
