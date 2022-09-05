<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\{
    User,
    Complainant,
    Respondent
};
class Note extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['complainant_id', 'notes'];

    protected $hidden = ['updated_at', 'deleted_at'];

    public function complainant_user()
    {
        return $this->belongsTo(User::class, 'complainant_user_id');
    }

    public function respondent_user()
    {
        return $this->belongsTo(User::class, 'respondent_user_id');
    }

    public function hr_user()
    {
        return $this->belongsTo(User::class, 'hr_user_id');
    }

    public function invite_user()
    {
        return $this->belongsTo(User::class, 'invite_user_id');
    }
}
