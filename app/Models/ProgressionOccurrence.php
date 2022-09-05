<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Respondent;
use App\Models\Admin\Settings\Coc\{Offense, AttendancePointsSystem};

class ProgressionOccurrence extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id',
        'offense_id',
        'first_respondent_id',
        'second_respondent_id',
        'third_respondent_id',
        'fourth_respondent_id',
        'fifth_respondent_id',
        'sixth_respondent_id',
        'seventh_respondent_id',
        'first_occurrence',
        'second_occurrence',
        'third_occurrence',
        'fourth_occurrence',
        'fifth_occurrence',
        'sixth_occurrence',
        'seventh_occurrence'
    ];

    protected $hidden = ['created_at'];

    public function offense()
    {
        return $this->belongsTo(Offense::class, 'offense_id')->withTrashed();
    }

    public function attendancepointssystem()
    {
        return $this->belongsTo(AttendancePointsSystem::class, 'attendance_points_system_id');
    }

    public function employee_user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function first_respondent()
    {
        return $this->belongsTo(Respondent::class, 'first_respondent_id');
    }

    public function second_respondent()
    {
        return $this->belongsTo(Respondent::class, 'second_respondent_id');
    }

    public function third_respondent()
    {
        return $this->belongsTo(Respondent::class, 'third_respondent_id');
    }

    public function fourth_respondent()
    {
        return $this->belongsTo(Respondent::class, 'fourth_respondent_id');
    }

    public function fifth_respondent()
    {
        return $this->belongsTo(Respondent::class, 'fifth_respondent_id');
    }

    public function sixth_respondent()
    {
        return $this->belongsTo(Respondent::class, 'sixth_respondent_id');
    }

    public function seventh_respondent()
    {
        return $this->belongsTo(Respondent::class, 'seventh_respondent_id');
    }
}
