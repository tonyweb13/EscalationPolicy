<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class ManagerRatingSiteLeadForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'manager_rating_site_lead_forms';

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
        'monthly_data_target',
        'monthly_data_weight',
        'monthly_data_rating_scale',
        'monthly_data_final_score',
        'monthly_site_target',
        'monthly_site_weight',
        'monthly_site_rating_scale',
        'monthly_site_final_score',
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
