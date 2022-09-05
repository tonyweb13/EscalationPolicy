<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\User;

class Witness extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['complainant_id', 'witness_user_id', 'hr_user_id', 'complainant_user_id', 'ir_id'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function witness_fullname()
    {
        return $this->belongsTo(User::class, 'witness_user_id');
    }
}
