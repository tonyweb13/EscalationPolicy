<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class AdminForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'admin_forms';

    protected $fillable = [
        'attendance_reliability_target',
        'attendance_reliability_actual_rating',
        'attendance_reliability_weight_of_kpi',
        'attendance_reliability_rating',
        'attendance_reliability_comment',
        'punctuality_target',
        'punctuality_actual_rating',
        'punctuality_weight_of_kpi',
        'punctuality_rating',
        'punctuality_comment',
        'process_knowledge_target',
        'process_knowledge_rating',
        'process_knowledge_weight_of_kpi',
        'process_knowledge_rating',
        'process_knowledge_comment',
        'work_ethics_number_target',
        'work_ethics_number_actual_rating',
        'work_ethics_number_weight_of_kpi',
        'work_ethics_number_rating',
        'work_ethics_number_comment',
        'work_ethics_no_nte_target',
        'work_ethics_no_nte_actual_rating',
        'work_ethics_no_nte_weight_of_kpi',
        'work_ethics_no_nte_rating',
        'work_ethics_no_nte_comment',
        'total',
        'development_plan',
        'weakness',
        'strengths',
        'managers_feedback',
        'employees_acknowledge',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
