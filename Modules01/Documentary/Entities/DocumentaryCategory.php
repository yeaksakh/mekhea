<?php

namespace Modules\Documentary\Entities;

use Illuminate\Database\Eloquent\Model;

class DocumentaryCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'documentary_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }

    public function documentary()
    {
        return $this->hasMany(Documentary::class, 'category_id');
    }

    // Relationship to parent category
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Relationship to child categories
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
