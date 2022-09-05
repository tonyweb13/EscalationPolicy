<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespondentNotResponse extends Model
{
    protected $fillable = ['ir_id', 'user_id', 'email_address'];

    protected $hidden = [ 'created_at', 'updated_at'];
}
