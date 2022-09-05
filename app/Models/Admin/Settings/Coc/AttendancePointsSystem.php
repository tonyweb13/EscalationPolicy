<?php

namespace App\Models\Admin\Settings\Coc;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Admin\Settings\Coc\{
    Category,
    AttendancePoint
};

class AttendancePointsSystem extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $dates = ['deleted_at'];

    protected $fillable = ['type_infraction', 'category_id', 'attendancepoint_id', 'definition'];

    protected $hidden = [ 'created_at', 'updated_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attendancepoint()
    {
        return $this->belongsTo(AttendancePoint::class);
    }
}
