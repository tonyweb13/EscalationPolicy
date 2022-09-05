<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Audit extends Model
{
    public function audit_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
