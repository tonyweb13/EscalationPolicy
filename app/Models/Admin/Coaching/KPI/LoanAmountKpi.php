<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;

class LoanAmountKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'loan_amount_kpi';

    protected $fillable = ['target', 'rating', 'computation', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];
}
