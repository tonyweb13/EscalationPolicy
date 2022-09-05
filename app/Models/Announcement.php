<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    AnnouncementRecipient,
    User
};

class Announcement extends Model
{
    protected $fillable = [
        'subject', 'content', 'attachment', 'sender_id', 'distro_category_id', 'created_at'
    ];

    protected $hidden = ['updated_at'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function compliance()
    {
        return $this->hasMany(AnnouncementRecipient::class, 'announcement_id');
    }
}
