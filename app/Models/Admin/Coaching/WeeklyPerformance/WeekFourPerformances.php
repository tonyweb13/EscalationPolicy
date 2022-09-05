<?php

namespace App\Models\Admin\Coaching\WeeklyPerformance;

use Illuminate\Database\Eloquent\Model;

class WeekFourPerformances extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'week_four_performances';

    protected $fillable = [
        'target_in_call',
        'target_ready',
        'target_wrap_up',
        'target_not_ready',
        'target_loan_origination',
        'target_qa_scores',
        'target_compliance',
        'target_attendance',
        'actual_in_call',
        'actual_ready',
        'actual_wrap_up',
        'actual_not_ready',
        'actual_loan_origination',
        'actual_qa_scores',
        'actual_compliance',
        'actual_attendance'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
