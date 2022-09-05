<?php

namespace App\Models\Admin\Coaching\KPI;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Coaching\KPI\{
    AttendanceReliabilityKpi,
    CoachingLogKpi,
    LoanAmountKpi,
    QaComplianceKpi,
    QaScoreKpi
};

class ScoreCardKpi extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'score_card_kpi';

    protected $fillable = [
        'stage',
        'loan_amount_kpi_id',
        'qa_compliance_kpi_id',
        'qa_score_kpi_id',
        'coaching_log_kpi_id',
        'attendance_reliability_kpi_id',
        'knowledge_kpi_id',
        'correction_kpi_id',
        'strengths',
        'weakness',
        'action_plans'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function loan_amount()
    {
        return $this->belongsTo(LoanAmountKpi::class, 'loan_amount_kpi_id');
    }

    public function coaching_log()
    {
        return $this->belongsTo(CoachingLogKpi::class, 'coaching_log_kpi_id');
    }

    public function qa_compliance()
    {
        return $this->belongsTo(QaComplianceKpi::class, 'qa_compliance_kpi_id');
    }

    public function qa_score()
    {
        return $this->belongsTo(QaScoreKpi::class, 'qa_score_kpi_id');
    }

    public function knowledge()
    {
        return $this->belongsTo(KnowledgeKpi::class, 'knowledge_kpi_id');
    }

    public function correction()
    {
        return $this->belongsTo(CorrectionKpi::class, 'correction_kpi_id');
    }

    public function attendance_reliability()
    {
        return $this->belongsTo(AttendanceReliabilityKpi::class, 'attendance_reliability_kpi_id');
    }
}
