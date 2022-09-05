<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class PayrollForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'payroll_forms';

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
        'payroll_reports_target',
        'payroll_reports_weight',
        'payroll_reports_rating_scale',
        'payroll_reports_final_score',
        'government_target',
        'government_weight',
        'government_rating_scale',
        'government_final_score',
        'bank_target',
        'bank_weight',
        'bank_rating_scale',
        'bank_final_score',
        'escalation_target',
        'escalation_weight',
        'escalation_rating_scale',
        'escalation_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
