<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    HrbpCluster,
    Respondent
};

class NteDaClosedStatus extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $connection = 'mysql';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id', 'last_name', 'first_name', 'position', 'site', 'hrbp', 'dm', 'immediate_supervisor',
        'date_of_offense', 'nte_request', 'date_nte_draft', 'coc_provision', 'description_of_the_offense',
        'gravity', 'nte_date_served', 'date_hr_received', 'date_admin_hearing', 'da_imposed', 'date_da',
        'stage_of_the_case', 'notes_for_case', 'case_status', 'ageing', 'days_exceeded_tat', 'tat_status',
        'year_nte_draft', 'month_nte_draft', 'week', 'quarter', 'rank'
    ];

    protected $hidden = ['created_at', 'deleted_at'];

    public function closed_hr()
    {
        return $this->belongsTo(HrbpCluster::class, 'employee_id', 'user_employee_no');
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'ir_number', 'ir_number');
    }
}
