<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfficiencyGoal extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql-coaching';

    protected $fillable = [
        'portfolio_name',
        'portfolio_id',
        'priority',
        'in_call',
        'ready',
        'wrap_up',
        'not_ready',
    ];
}
