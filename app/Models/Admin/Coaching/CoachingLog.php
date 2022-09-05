<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Coaching\KPI\ScoreCardKpi;
use App\Models\Admin\Coaching\WeeklyPerformance\WeeklyPerformances;
use App\Models\Admin\Settings\Coc\Offense;
use App\Models\{
    User,
    HrbpCluster
};
class CoachingLog extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'coaching_logs';

    protected $fillable = [
        'employee_no',
        'attachment_id',
        'form_type',
        'findings',
        'point_of_disscussion',
        'action_plans',
        'agents_commitment',
        'supervisors_commitment',
        'creted_by',
        'offense_id',
        'cl_number',
        'date_start',
        'date_end',
        'number_occurrence',
        'date_created'
    ];

    public function kpi()
    {
        return $this->belongsTo(ScoreCardKpi::class, 'attachment_id');
    }

    public function performance()
    {
        return $this->belongsTo(WeeklyPerformances::class, 'performance_id');
    }

    public function image_attachments()
    {
        return $this->belongsTo(ImageAttachment::class, 'image_attachment_id');
    }

    public function user_employee()
    {
        return $this->belongsTo(User::class, 'employee_no', 'employee_no');
    }

    public function hrbp_coaching()
    {
        return $this->belongsTo(HrbpCluster::class, 'employee_no', 'user_employee_no');
    }

    public function offense()
    {
        return $this->belongsTo(Offense::class, 'offense_id');
    }
}
