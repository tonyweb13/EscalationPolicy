<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class CoachingLogKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'coaching_log_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];
}
