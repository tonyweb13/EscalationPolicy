<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\User;

class AcknowledgementReceipt extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'incentive',
        'prize',
        'item',
        'date_received',
        'acknowledgement_confirmation'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function employee_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supervisor_user()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function manager_user()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
