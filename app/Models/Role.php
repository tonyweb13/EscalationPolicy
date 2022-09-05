<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Role extends Model
{
    protected $connection = 'mysql';

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
