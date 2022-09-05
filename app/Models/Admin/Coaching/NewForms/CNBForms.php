<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class CNBForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'c_n_b_forms';

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
        'payroll_file_target',
        'payroll_file_weight',
        'payroll_file_rating_scale',
        'payroll_file_final_score',
        'timekeeping_target',
        'timekeeping_weight',
        'timekeeping_rating_scale',
        'timekeeping_final_score',
        'processing_target',
        'processing_weight',
        'processing_rating_scale',
        'processing_final_score',
        'hmo_target',
        'hmo_weight',
        'hmo_rating_scale',
        'hmo_final_score',
        'inssuance_target',
        'inssuance_weight',
        'inssuance_rating_scale',
        'inssuance_final_score',
        'final_pay_target',
        'final_pay_weight',
        'final_pay_rating_scale',
        'final_pay_final_score',
        'total_score',
        'commendation',
        'action_plans',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
