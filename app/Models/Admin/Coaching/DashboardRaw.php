<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;

class DashboardRaw extends Model
{
    protected $connection = 'mysql-coaching';

    protected $fillable = [
        'employee_no',
        'tenureship',
        'portfolio',
        'coverage',
        'scg',
        'site_and_portfolio',
        'team',
        'loan_amount',
        'in_call',
        'ready',
        'wrap_up',
        'not_ready',
        'in_call_target',
        'ready_target',
        'wrap_up_target',
        'not_ready_target',
        'days',
        'scorecard_goal',
        'attendance_reliability',
        'report_date',
        'week_number',
        'site_location',
        'minor',
        'major',
        'running_week',
        'cluster',
        'mtd_total_loan_amount',
        'mtdg',
        'coverage_total_loan_amount',
        'attendance',
        'days',
        'update_status',
        'update_date'
    ];
}
