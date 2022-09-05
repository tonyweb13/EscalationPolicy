<?php

namespace App\Models\Admin\Settings\Coc;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Gravity extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['gravity'];

    protected $hidden = [ 'created_at', 'updated_at', 'deleted_at'];
}
