<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ComplainantEditRequest extends Model
{
    protected $fillable = [
        'ir_id',
        'respondent_id',
        'ir_number',
        'request_status',
        'requestor_id',
        'approver_id',
        'reason'
    ];

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
