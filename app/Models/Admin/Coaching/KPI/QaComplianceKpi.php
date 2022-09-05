<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class QaComplianceKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'qa_compliance_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];
}
