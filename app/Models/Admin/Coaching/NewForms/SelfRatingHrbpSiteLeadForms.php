<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class SelfRatingHrbpSiteLeadForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'self_rating_hrbp_site_lead_forms';

    protected $fillable = [
        'work_ethics_target',
        'work_ethics_weight',
        'work_ethics_rating_scale',
        'work_ethics_final_score',
        'leadership_target',
        'leadership_weight',
        'leadership_rating_scale',
        'leadership_final_score',
        'attendance_reliability_target',
        'attendance_reliability_weight',
        'attendance_reliability_rating_scale',
        'attendance_reliability_final_score',
        'analysis_target',
        'analysis_weight',
        'analysis_rating_scale',
        'analysis_final_score',
        'satisfaction_target',
        'satisfaction_weight',
        'satisfaction_rating_scale',
        'satisfaction_final_score',
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
