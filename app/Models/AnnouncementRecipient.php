<?php

namespace App\Models;

use App\Models\{
    User,
    Announcement,
    AnnouncementAttachment,
    DistroCategory
};

use Illuminate\Database\Eloquent\Model;

class AnnouncementRecipient extends Model
{
    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function announce()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }

    public function announce_attached()
    {
        return $this->hasMany(AnnouncementAttachment::class, 'announcement_id', 'announcement_id');
    }

    public function distro()
    {
        return $this->belongsTo(DistroCategory::class, 'distro_category_id');
    }
}
