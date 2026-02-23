<?php

namespace Modules\News\Entities;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'news_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(NewsCategory::class, 'category_id');
        }
        
}