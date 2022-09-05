<?php

namespace App\Models\Admin\Coaching\WeeklyPerformance;

use Illuminate\Database\Eloquent\Model;

class WeightPerformances extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'weight_performances';

    protected $fillable = [
        'loan_amount',
        'qa_score',
        'correction',
        'compliance',
        'attendance_reliability',
        'total'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
