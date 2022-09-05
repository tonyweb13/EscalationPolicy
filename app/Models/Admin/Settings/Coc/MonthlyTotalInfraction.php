<?php

namespace App\Models\Admin\Settings\Coc;

use Illuminate\Database\Eloquent\Model;

class MonthlyTotalInfraction extends Model
{
    protected $fillable = [
        'employee_no',
        'last_name',
        'first_name',
        'grave',
        'major',
        'minor',
        'serious',
        'grand_total'
    ];

    protected $hidden = [ 'created_at', 'updated_at'];
}
