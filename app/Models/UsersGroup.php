<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    HrbpCluster
};

class UsersGroup extends Model
{

    protected $connection = 'mysql-customdb1';

    /* beta version */
    protected $database = 'db_vps';

    protected $table = 'users_groups';

    protected $fillable = ['employee_no', 'portfolio_id', 'group_id', 'teamlead_employee_no'];

    protected $hidden = ['created_at', 'updated_at'];

    public function supervisor_assign()
    {
        return $this->belongsTo(User::class, 'teamlead_employee_no', 'employee_no');
    }

    public function hr_assign()
    {
        return $this->belongsTo(HrbpCluster::class, 'employee_no', 'user_employee_no');
    }

    public function employee_assign()
    {
        return $this->belongsTo(User::class, 'employee_no', 'employee_no');
    }

    public function supervisor_assign_multiple()
    {
        return $this->hasMany(User::class, 'teamlead_employee_no', 'employee_no');
    }
}
