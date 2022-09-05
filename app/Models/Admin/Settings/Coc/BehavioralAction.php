<?php

namespace App\Models\Admin\Settings\Coc;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Admin\Settings\Coc\{
    Gravity,
    IncidentReportResolution
};

class BehavioralAction extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'gravity_id',
        'first_irr',
        'second_irr',
        'third_irr',
        'fourth_irr',
        'fifth_irr',
        'sixth_irr',
        'seventh_irr'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function gravity_offense()
    {
        return $this->belongsTo(Gravity::class, 'gravity_id');
    }

    public function first_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'first_irr');
    }

    public function second_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'second_irr');
    }

    public function third_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'third_irr');
    }

    public function fourth_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'fourth_irr');
    }

    public function fifth_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'fifth_irr');
    }

    public function sixth_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'sixth_irr');
    }

    public function seventh_occur()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'seventh_irr');
    }
}
