<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rules extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';

    protected $dates = ['deleted_at'];

    protected $table = 'rules';

    protected $hidden = ['deleted_at'];

    protected $casts = [
        'rules' => 'array',
    ];

    protected $fillable = ['employee_no', 'rules'];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_no');
    }
}
