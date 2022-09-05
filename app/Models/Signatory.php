<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    protected $table = 'signatories';

    protected $fillable = [
        'prepared_by_first',
        'prepared_by_last',
        'prepared_by_designation',
        'prepared_by_empno',
        'requested_by_first',
        'requested_by_last',
        'requested_by_designation',
        'requested_by_empno',
        'approved_by_first',
        'approved_by_last',
        'approved_by_designation',
        'approved_by_empno',
        'noted1_by_first',
        'noted1_by_last',
        'noted1_by_designation',
        'noted1_by_empno',
        'noted2_by_first',
        'noted2_by_last',
        'noted2_by_designation',
        'noted2_by_empno',
        'noted3_by_first',
        'noted3_by_last',
        'noted3_by_designation',
        'noted3_by_empno',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
