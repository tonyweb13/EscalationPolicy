<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ReopenRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ir_id',
        'respondent_id',
        'ir_number',
        'status_id',
        'request_status',
        'requestor_id',
        'approver_id',
        'reason'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public function requestor_name()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function approver_name()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
