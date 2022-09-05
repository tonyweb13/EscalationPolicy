<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;

class DisputeDashboard extends Model
{
    protected $connection = 'mysql-coaching';

    protected $fillable = [
        'employee_no',
        'created_by',
        'date_updated',
        'dashboard_raw_id'
    ];
}
