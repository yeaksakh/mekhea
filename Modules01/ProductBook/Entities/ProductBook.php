<?php

namespace Modules\ProductBook\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductBook extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'productbook_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(ProductBookCategory::class, 'category_id');
        }
        
}