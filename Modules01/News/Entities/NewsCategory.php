<?php

namespace Modules\News\Entities;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'news_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}