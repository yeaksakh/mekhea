<?php

namespace Modules\SOP\Entities;

use Illuminate\Database\Eloquent\Model;

class SOP extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'sop_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(SOPCategory::class, 'category_id');
        }
        
}