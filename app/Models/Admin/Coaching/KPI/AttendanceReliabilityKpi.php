<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class AttendanceReliabilityKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'attendance_reliability_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];



}
