<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    Admin\IncidentReport,
    User,
    Complainant,
    ProgressionOffense,
    ProgressionOccurrence,
    HearingAttendance,
    SignedDocument,
    SupervisorApproval,
    UsersGroup,
    ReopenRequest,
    ComplainantEditRequest
};
use PhpOffice\PhpSpreadsheet\Style\Supervisor;

class Respondent extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'complainant_id',
        'respondent_user_id',
        'response',
        'hrbp_acknowledge_response',
        'email_acknowledge_date',
        'new_ageing',
        'start_date',
        'last_date',
        'takes_date',
        'type_of_date',
        'hrbp_employee_no',
        'hrsl_employee_no',
        'first_comment',
        'second_comment',
        'mom',
    ];

    protected $hidden = ['deleted_at'];

    public function complainant()
    {
        return $this->belongsTo(Complainant::class, 'complainant_id');
    }

    public function offense()
    {
        return $this->belongsTo(Offense::class, 'offense_id');
    }

    public function notes()
    {
        return $this->belongsTo(Note::class, 'notes_id');
    }

    public function attached_hr()
    {
        return $this->hasMany(Attachment::class, 'respondent_id');
    }

    public function incident_report()
    {
        return $this->belongsTo(IncidentReport::class, 'ir_id');
    }

    public function signed()
    {
        return $this->belongsTo(SignedDocument::class, 'ir_id', 'ir_id');
    }

    /*Incident Report*/
    public function respondent_user()
    {
        return $this->belongsTo(User::class, 'respondent_user_id');
    }

    /*Progression Occurence */
    public function progression_occurence()
    {
        return $this->hasMany(ProgressionOccurrence::class, 'employee_id', 'respondent_user_id');
    }

    /* Progression Offense */
    public function progression_offense()
    {
        return $this->belongsTo(ProgressionOffense::class, 'respondent_user_id', 'employee_id');
    }

    public function progression_schedule_adherence()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'schedule_adherence_po_id');
    }

    public function progression_performance_related()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'performance_related_po_id');
    }

    public function progression_behaviour_related()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'behaviour_related_po_id');
    }

    public function progression_information_security()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'information_security_po_id');
    }

    public function progression_breach_trust()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'breach_trust_po_id');
    }

    public function progression_gross_negligence()
    {
        return $this->belongsTo(ProgressionOffense::class, 'id', 'gross_negligence_po_id');
    }

    public function respondent_hearing()
    {
        return $this->belongsTo(HearingAttendance::class, 'ir_id', 'ir_id')->where('requestor', "Respondent");
    }

    public function invite_ir()
    {
        return $this->belongsTo(HearingAttendance::class, 'ir_id', 'ir_id');
    }

    public function hrbp_user()
    {
        return $this->belongsTo(User::class, 'hrbp_employee_no', 'employee_no');
    }

    public function hrsl_user()
    {
        return $this->belongsTo(User::class, 'hrsl_employee_no', 'employee_no');
    }

    public function supervisor_approval()
    {
        return $this->belongsTo(SupervisorApproval::class, 'id', 'ir_id');
    }

    public function user_group_cluster()
    {
        return $this->hasMany(UsersGroup::class, 'employee_no', 'teamlead_employee_no');
    }

    public function reopen()
    {
        return $this->belongsTo(ReopenRequest::class, 'id', 'respondent_id');
    }

    public function complainant_request()
    {
        return $this->belongsTo(ComplainantEditRequest::class, 'id', 'respondent_id');
    }

    public function onhold_request()
    {
        return $this->belongsTo(OnHoldRequest::class, 'id', 'respondent_id');
    }

    public function respondent_attachments()
    {
        return $this->hasMany(Attachment::class, 'respondent_id');
    }
}
