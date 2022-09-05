<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DistroEmail;

class DistroCategory extends Model
{
    protected $fillable = [
        'distro_name'
    ];

    protected $hidden = ['updated_at'];

    public function distro_categories()
    {
        return $this->hasMany(DistroEmail::class, 'distro_category_id');
    }
}
