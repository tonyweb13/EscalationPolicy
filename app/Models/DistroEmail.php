<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DistroEmail extends Model
{
    public function distro_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
