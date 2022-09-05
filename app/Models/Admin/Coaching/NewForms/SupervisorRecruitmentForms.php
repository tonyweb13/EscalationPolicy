<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class SupervisorRecruitmentForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'supervisor_recruitment_forms';

    protected $fillable = [
        'support_hiring_target',
        'support_hiring_actual_rating',
        'support_hiring_weight',
        'support_hiring_rating_scale',
        'support_hiring_comments',
        'agent_hiring_target',
        'agent_hiring_actual_rating',
        'agent_hiring_weight',
        'agent_hiring_rating_scale',
        'agent_hiring_comments',
        'development_target',
        'development_actual_rating',
        'development_weight',
        'development_rating_scale',
        'development_comments',
        'escalation_target',
        'escalation_actual_rating',
        'escalation_weight',
        'escalation_rating_scale',
        'escalation_comments',
        'infraction_target',
        'infraction_actual_rating',
        'infraction_weight',
        'infraction_rating_scale',
        'infraction_comments',
        'recruitment_teams_target',
        'recruitment_teams_actual_rating',
        'recruitment_teams_weight',
        'recruitment_teams_rating_scale',
        'recruitment_teams_comments',
        'total_score',
        'development_plan',
        'weaknesses',
        'strengths',
        'managers_feedback',
        'employees_acknowledge',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
