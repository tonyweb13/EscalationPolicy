<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class QaScoreKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'qa_score_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];
}
