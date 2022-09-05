<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Admin\Settings\Coc\IncidentReportResolution,
    User,
    Respondent,
    ProgressionOccurrence
};

class ProgressionOffense extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id',
        'schedule_adherence_po_id',
        'performance_related_po_id',
        'behaviour_related_po_id',
        'information_security_po_id',
        'breach_trust_po_id',
        'gross_negligence_po_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function po_schedule_adherence()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'schedule_adherence_po_id');
    }

    public function po_performance_related()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'performance_related_po_id');
    }

    public function po_behaviour_related()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'behaviour_related_po_id');
    }

    public function po_information_security()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'information_security_po_id');
    }

    public function po_breach_trust()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'breach_trust_po_id');
    }

    public function po_gross_negligence()
    {
        return $this->belongsTo(ProgressionOccurrence::class, 'gross_negligence_po_id');
    }

    public function employee_user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

}
