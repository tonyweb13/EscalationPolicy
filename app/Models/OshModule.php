<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OshModule extends Model
{

    protected $fillable = [
        'user_id',
        'date_finish',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function employee_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
