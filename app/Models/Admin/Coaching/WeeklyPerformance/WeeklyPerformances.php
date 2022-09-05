<?php

namespace App\Models\Admin\Coaching\WeeklyPerformance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Coaching\KPI\ScoreCardKpi;
use App\Models\Admin\Coaching\WeeklyPerformance\{
    WeekOnePerformances,
    WeekTwoPerformances,
    WeekThreePerformances,
    WeekFourPerformances,
    WeekFivePerformances,
    WeightPerformances,
    CurrentScorePerformances,
    RatingRightPerformances,
    RatingLeftPerformances,
};

class WeeklyPerformances extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'weekly_performances';

    protected $fillable = [
        'employee_no',
        'identified_behavior',
        'root_cause',
        'action_plans',
        'base_performance',
        'goal',
        'extended_action_plan',
        'justification',
        'additional_feedback',
        'target_in_call',
        'target_ready',
        'target_wrap_up',
        'target_not_ready',
        'target_loan_origination',
        'target_qa_scores',
        'target_compliance',
        'week1_id',
        'week2_id',
        'week3_id',
        'week4_id',
        'week5_id',
        'weight_id',
        'current_score_id',
        'rating_left_side_id',
        'rating_right_side_id'
    ];

    public function week_one()
    {
        return $this->belongsTo(WeekOnePerformances::class, 'week1_id');
    }

    public function week_two()
    {
        return $this->belongsTo(WeekTwoPerformances::class, 'week2_id');
    }

    public function week_three()
    {
        return $this->belongsTo(WeekThreePerformances::class, 'week3_id');
    }

    public function week_four()
    {
        return $this->belongsTo(WeekFourPerformances::class, 'week4_id');
    }

    public function week_five()
    {
        return $this->belongsTo(WeekFivePerformances::class, 'week5_id');
    }

    public function weight()
    {
        return $this->belongsTo(WeightPerformances::class, 'weight_id');
    }

    public function current_score()
    {
        return $this->belongsTo(CurrentScorePerformances::class, 'current_score_id');
    }

    public function rating_right()
    {
        return $this->belongsTo(RatingRightPerformances::class, 'rating_right_side_id');
    }

    public function rating_left()
    {
        return $this->belongsTo(RatingLeftPerformances::class, 'rating_left_side_id');
    }
}
