<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;

class KeyPerformanceIndicator extends Model
{
    protected $connection = 'mysql-coaching';

    protected $fillable = [
        'fifth_indicator',
        'fourth_indicator',
        'third_indicator',
        'second_indicator',
        'first_indicator'
    ];
}
