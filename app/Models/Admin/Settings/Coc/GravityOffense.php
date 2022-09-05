<?php

namespace App\Models\Admin\Settings\Coc;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Admin\Settings\Coc\Gravity;

class GravityOffense extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['fields','gravity_id', 'description'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function gravity()
    {
        return $this->belongsTo(Gravity::class);
    }
}
