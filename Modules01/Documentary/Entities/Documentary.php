<?php

namespace Modules\Documentary\Entities;

use Illuminate\Database\Eloquent\Model;

class Documentary extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'documentary_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(DocumentaryCategory::class, 'category_id');
        }
        
}