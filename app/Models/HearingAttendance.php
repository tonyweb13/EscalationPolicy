<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Invite,
    User
};

class HearingAttendance extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['going', 'complainant_id', 'respondent_id', 'ir_number',
        'scheduled_admin_date', 'scheduled_admin_time', 'scheduled_admin_place', 'requestor', 'accepted_date',
        'requestor_user_id', 'reason', 'suggested_date', 'suggested_time', 'suggested_place', 'hr_approval'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function hearing_invite()
    {
        return $this->belongsTo(Invite::class, 'requestor_user_id', 'invite_user_id');
    }

    public function invite_ir_fullname()
    {
        return $this->belongsTo(User::class, 'requestor_user_id');
    }
}
