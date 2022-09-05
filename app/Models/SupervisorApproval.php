<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SupervisorApproval extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['supervisor_user_id', 'respondent_user_id', 'complainant_id', 'ir_id', 'approver_status'];

    protected $hidden = ['updated_at', 'deleted_at'];
}
