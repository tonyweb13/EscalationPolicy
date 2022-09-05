<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    User,
    UsersGroup,
    OfficeLocation
};

class HrbpCluster extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $connection = 'mysql';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'hrsl_employee_no', 'hrsl_email_address', 'hrbp_employee_no',
        'hrbp_email_address', 'hrsl_office_location_id', 'user_employee_no'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function teamlead_cluster()
    {
        return $this->hasMany(UsersGroup::class, 'employee_no', 'teamlead_employee_no');
    }

    public function hrsl_user()
    {
        return $this->belongsTo(User::class, 'hrsl_employee_no', 'employee_no');
    }

    public function hrbp_user()
    {
        return $this->belongsTo(User::class, 'hrbp_employee_no', 'employee_no');
    }

    public function site_office()
    {
        return $this->belongsTo(OfficeLocation::class, 'hrsl_office_location_id');
    }

    public function cluster_alignment()
    {
        return $this->belongsTo(UsersGroup::class, 'user_employee_no', 'employee_no');
    }

    public function employee_current()
    {
        return $this->belongsTo(User::class, 'user_employee_no', 'employee_no');
    }

    // public function employee_under()
    // {
    //     return $this->belongsTo(UsersGroup::class, 'user_employee_no', 'teamlead_employee_no');
    // }

        // public function current_user()
    // {
    //     return $this->belongsTo(User::class, 'user_employee_no', 'employee_no');
    // }
}
