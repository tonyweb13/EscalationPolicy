<?php

namespace App\Models\Admin;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Admin\Settings\Coc\IncidentReportResolution,
    User,
    Witness,
    Invite,
    Respondent,
    HearingAttendance,
    Attachment,
    Signatory
};

class IncidentReport extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'hr_user_id', 'complainant_id', 'remarks', 'is_generate_nte_invalid_ir', 'date_admin_hearing', 
        'time_admin_hearing', 'meeting_place', 'is_under_investigation', 'irr_id', 'witness_user_id', 
        'invite_user_id', 'type_invite', 'ageing', 'rtw', 'rtw_date', 'rtw_advice_date', 
        'sn_termination_date', 'sn_date_absence_start', 'sn_date_absence_end', 'agreement', 
        'date_da', 'preventive_suspension_start', 'preventive_suspension_end', 'additional_notes'
    ];

    protected $hidden = ['deleted_at'];

    public function invite_ir()
    {
        return $this->belongsTo(HearingAttendance::class, 'id', 'ir_id')->where('requestor_user_id', Auth()->id());
    }

    public function invite_hearing()
    {
        return $this->hasMany(HearingAttendance::class, 'ir_id')->where('requestor', 'Invite');
    }

    public function invite_user()
    {
        return $this->hasMany(Invite::class, 'complainant_id', 'complainant_id');
    }

    public function witness_user()
    {
        return $this->hasMany(Witness::class, 'ir_id');
    }

    public function respondent_user()
    {
        return $this->belongsTo(User::class, 'respondent_user_id');
    }

    public function hrbp_user()
    {
        return $this->belongsTo(User::class, 'hr_user_id');
    }

    public function irr()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'irr_id')->withTrashed();
        /***** change to with trashed *****/
        // return $this->belongsTo(IncidentReportResolution::class, 'irr_id');
    }

    public function respondent_attendance()
    {
        return $this->belongsTo(HearingAttendance::class, 'id', 'ir_id');
    }

    public function hr_attachments()
    {
        return $this->belongsTo(Attachment::class, 'hr_attachments_id');
    }

    public function mom_attachment()
    {
        return $this->belongsTo(Attachment::class, 'minutes_of_the_meeting_attachment_id');
    }


    public function initial_irr()
    {
        return $this->belongsTo(IncidentReportResolution::class, 'initial_irr_id')->withTrashed();
        /***** change to with trashed *****/
        // return $this->belongsTo(IncidentReportResolution::class, 'initial_irr_id');
    }

    public function ir_signatories()
    {
        return $this->belongsTo(Signatory::class, 'id', 'ir_id');
    }
}
