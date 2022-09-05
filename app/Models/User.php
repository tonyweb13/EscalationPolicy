<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\{
    Department,
    Designation,
    OfficeLocation,
    UsersGroup,
    Address
};

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mysql-customdb1';

    protected $database = 'db_vps';

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
        'username',
        'middle_name',
        'birthday',
        'gender',
        'tax_status_id',
        'civil_status',
        'mobile_no',
        'employment_type_id',
        'date_hired',
        'regularization_date',
        'educational_institution',
        'government_credential_id',
        'is_default_password',
        'is_active',
        'course',
        'year_graduated',
        'bank_type',
        'bank_account_no',
        'wave',
        'person_to_contact_in_case_of_emergency',
        'contact_person_no',
        'relationship',
        'created_at',
        'updated_at'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function office_location()
    {
        return $this->belongsTo(OfficeLocation::class, 'work_location_id');
    }

    public function employee_supervisor()
    {
        return $this->belongsTo(UsersGroup::class, 'employee_no', 'employee_no');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user',
            'user_id',
            'role_id'
        );
    }

    public function rules()
    {
        return $this->hasOne(
            Rules::class,
            'employee_no',
            'employee_no'
        );
    }

    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute) {
            parent::setAttribute($key, $value);
        }
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // public function user_group_access()
    // {
    //     return $this->belongsTo(UsersGroup::class, 'employee_no', 'employee_no');
    // }
}
