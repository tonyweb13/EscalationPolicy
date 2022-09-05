<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;

class MonthlyPerformance extends Model
{
    protected $connection = 'mysql-coaching';

    protected $fillable = [
        'name',
        'scg',
        'employee_no',
        'supervisor',
        'department',
        'designation',
        'tenureship',
        'coverage',
        'portfolio',
        'in_call',
        'end_scg',
        'ready',
        'not_ready',
        'wrap_up',
        'in_call_ave',
        'ready_ave',
        'not_ready_ave',
        'wrap_up_ave',
        'in_call_tag',
        'ready_tag',
        'not_ready_tag',
        'wrap_up_tag',
        'loan_amount',
        'loan_amount_score',
        'loan_amount_point',
        'loan_amount_ave',
        'weekly',
        'monthly',
        'created_by',
        'date_created',
        'attendance_reliability',
        'days',
        'attendance_score',
        'attendance_point'
    ];
}
