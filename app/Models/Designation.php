<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $connection = 'mysql-customdb1';

    protected $table = 'designation';

    protected $fillable = ['name'];

    protected $hidden = [ 'created_at', 'updated_at'];
}
