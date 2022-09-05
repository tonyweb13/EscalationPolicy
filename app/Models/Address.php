<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $connection = 'mysql-customdb1';

    /* beta version */
    // protected $database = 'db_vps';

    protected $table = 'addresses';

    protected $fillable = ['current_address', 'permanent_address'];

    protected $hidden = ['created_at', 'updated_at'];
}
