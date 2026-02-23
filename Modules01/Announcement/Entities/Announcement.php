<?php

namespace Modules\Announcement\Entities;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'announcement_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(AnnouncementCategory::class, 'category_id');
        }
        
}