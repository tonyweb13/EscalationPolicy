<?php

namespace App\Models;

use App\Models\{
    User,
    Respondent,
    HrbpCluster
};

use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $connection = 'mysql-customdb1';

    protected $database = 'db_vps';

    protected $table = 'office_locations';

    protected $fillable = ['name', 'description'];

    protected $hidden = ['created_at', 'updated_at'];
}
