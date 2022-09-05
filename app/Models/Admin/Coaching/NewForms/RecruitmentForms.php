<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class RecruitmentForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'recruitment_forms';

    protected $fillable = [
        'number_of_hires_target',
        'number_of_hires_actual',
        'number_of_hires_weight',
        'number_of_hires_rating_scale',
        'number_of_hires_comment',
        'time_to_fill_target',
        'time_to_fill_actual',
        'time_to_fill_weight',
        'time_to_fill_rating_scale',
        'time_to_fill_comment',
        'work_ethics_number_target',
        'work_ethics_number_actual',
        'work_ethics_number_weight',
        'work_ethics_number_rating_scale',
        'work_ethics_number_comment',
        'work_ethics_nte_target',
        'work_ethics_nte_actual',
        'work_ethics_nte_weight',
        'work_ethics_nte_rating_scale',
        'work_ethics_nte_comment',
        'recruitment_target',
        'recruitment_actual_rating',
        'recruitment_weight',
        'recruitment_rating_scale',
        'recruitment_comment',
        'total_score',
        'development_plan',
        'weakness',
        'strengths',
        'managers_feedback',
        'employees_acknowledge',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
