<?php

namespace Modules\Announcement\Entities;

use Illuminate\Database\Eloquent\Model;

class AnnouncementCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'announcement_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function announcement()
    {
        return $this->hasMany(Announcement::class, 'category_id');
    }
}