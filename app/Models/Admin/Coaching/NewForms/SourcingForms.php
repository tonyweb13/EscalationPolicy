<?php

namespace App\Models\Admin\Coaching\NewForms;

use Illuminate\Database\Eloquent\Model;

class SourcingForms extends Model
{
    protected $connection = 'mysql-coaching';

    protected $table = 'sourcing_forms';

    protected $fillable = [
        'show_up_target',
        'show_up_actual_rating',
        'show_up_weight',
        'show_up_rating_scale',
        'show_up_comment',
        'time_to_fill_target',
        'time_to_fill_actual_rating',
        'time_to_fill_weight',
        'time_to_fill_rating_scale',
        'time_to_fill_comment',
        'escalation_target',
        'escalation_actual_rating',
        'escalation_weight',
        'escalation_rating_scale',
        'escalation_comment',
        'no_nte_target',
        'no_nte_actual_rating',
        'no_nte_weight',
        'no_nte_rating_scale',
        'no_nte_comment',
        'initiative_target',
        'initiative_actual_rating',
        'initiative_weight',
        'initiative_rating_scale',
        'initiative_comment',
        'total_score',
        'development_plan',
        'weaknesses',
        'strengths',
        'managers_feedback',
        'employees_acknowledge',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
