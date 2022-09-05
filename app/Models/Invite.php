<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    User,
    HearingAttendance
};

class Invite extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['complainant_id', 'invite_user_id'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function invite_fullname()
    {
        return $this->belongsTo(User::class, 'invite_user_id');
    }

    public function invite_hearing()
    {
        return $this->belongsTo(HearingAttendance::class, 'invite_user_id', 'requestor_user_id');
    }
}
