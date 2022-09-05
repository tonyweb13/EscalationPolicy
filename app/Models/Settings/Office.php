<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'offices';

    protected $fillable = ['site', 'complete_address'];

    protected $hidden = ['created_at', 'updated_at'];
}
