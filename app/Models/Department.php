<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'mysql-customdb1';

    protected $fillable = ['name','description'];

    protected $hidden = [ 'created_at', 'updated_at'];
}
