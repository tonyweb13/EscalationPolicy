<?php

namespace App\Models\Admin\Settings\Coc;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Admin\Settings\Coc\IncidentReportResolution;


class Instance extends Model implements AuditableContract
{
    use Auditable;

    protected $connection = 'mysql';

    protected $fillable = [
        'offense_id',
        'first_instance_coc_id',
        'second_instance_coc_id',
        'third_instance_coc_id',
        'fourth_instance_coc_id',
        'fifth_instance_coc_id',
        'sixth_instance_coc_id',
        'seventh_instance_coc_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function first_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'first_instance_coc_id');
    }
    public function second_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'second_instance_coc_id');
    }
    public function third_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'third_instance_coc_id');
    }
    public function fourth_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'fourth_instance_coc_id');
    }
    public function fifth_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'fifth_instance_coc_id');
    }
    public function sixth_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'sixth_instance_coc_id');
    }
    public function seventh_instance()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'seventh_instance_coc_id');
    }
}
