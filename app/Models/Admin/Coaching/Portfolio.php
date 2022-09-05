<?php

namespace App\Models\Admin\Coaching;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $connection = 'mysql-loan';

    protected $fillable = [
        'name',
        'description'
    ];
}
