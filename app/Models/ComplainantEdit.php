<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplainantEdit extends Model
{
    protected $connection = 'mysql';

    protected $fillable = ['ir_number','column_edit', 'old_value', 'new_value', 'approver_id', 'complainant_user_id'];

    protected $hidden = [ 'created_at', 'updated_at'];
}
