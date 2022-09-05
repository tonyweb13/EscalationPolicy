<?php

namespace App\Models\Admin\Coaching;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ImageAttachment extends Model implements AuditableContract
{
    use Auditable;
    protected $connection = 'mysql-coaching';

    protected $fillable = ['attachments','cl_number'];

    protected $hidden = ['created_at', 'updated_at'];
}
