<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGreeting extends Model
{
    protected $fillable = [
        'template1',
        'template2',
        // 'profile_pic',
        'employee_name',
        'employee_number',
        'email_address',
        'birthday',
        'is_sent',
        'created_at',
    ];

    protected $hidden = ['updated_at'];
}
