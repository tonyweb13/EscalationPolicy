<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Admin\Settings\Coc\Offense,
    Admin\Settings\Coc\AttendancePointsSystem,
    User,
    Note,
    Attachment,
    Respondent,
    Witness,
    OfficeLocation
};

class Complainant extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'ir_number', 'complainant_user_id', 'offense_id', 'date_incident_start', 'description',
        'attachment_type'
    ];

    protected $hidden = ['updated_at', 'deleted_at'];

    public function complainant_user()
    {
        return $this->belongsTo(User::class, 'complainant_user_id');
    }

    public function witness_user()
    {
        return $this->hasMany(Witness::class, 'complainant_id')->where('hr_user_id', 0);
    }

    public function offense()
    {
        return $this->belongsTo(Offense::class)->withTrashed();
    }

    public function notes()
    {
        return $this->belongsTo(Note::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'complainant_id');
    }

    /*Incident Report*/
    public function respondent()
    {
        return $this->hasMany(Respondent::class, 'complainant_id');
    }

    public function attendancepointssystem()
    {
        return $this->belongsTo(AttendancePointsSystem::class);
    }

    public function notify_notes()
    {
        return $this->belongsTo(Note::class, 'id', 'complainant_id');
    }
}
