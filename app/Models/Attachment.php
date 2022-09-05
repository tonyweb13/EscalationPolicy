<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Attachment extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['attachment'];

    protected $hidden = ['created_at', 'updated_at'];
}
