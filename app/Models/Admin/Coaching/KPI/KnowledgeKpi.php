<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class KnowledgeKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'knowledge_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];
}
