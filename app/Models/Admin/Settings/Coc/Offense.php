<?php

namespace App\Models\Admin\Settings\Coc;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Admin\Settings\Coc\{
    Category,
    Gravity,
    Instance,
    BehavioralAction
};

class Offense extends Model implements AuditableContract
{
    use SoftDeletes;

    use Auditable;

    protected $connection = 'mysql';

    protected $dates = ['deleted_at'];

    protected $fillable = ['offense', 'category_id', 'gravity_id', 'instance_id', 'description'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function gravity()
    {
        return $this->belongsTo(Gravity::class);
    }

    public function behavioral_action()
    {
        return $this->belongsTo(BehavioralAction::class, 'gravity_id');
    }

    public function instance()
    {
        return $this->belongsTo(Instance::class, 'instance_id');
    }
}
