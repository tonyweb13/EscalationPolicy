<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Complainant,
    Respondent
};

class SignedDocument extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['ir_id', 'ir_number', 'complainant_id', 'nte_upload', 'irr_upload'];

    protected $hidden = ['updated_at', 'deleted_at'];

    public function complainant()
    {
        return $this->belongsTo(Complainant::class, 'complainant_id');
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'ir_id', 'ir_id');
    }
}
