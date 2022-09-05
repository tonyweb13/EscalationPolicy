<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAttachment extends Model
{
    protected $fillable = [
        'announcement_id', 'attachments'
    ];

    protected $hidden = ['updated_at'];
}
