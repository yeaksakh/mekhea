<?php

namespace Modules\SWOT\Entities;

use Illuminate\Database\Eloquent\Model;

class SWOT extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'swot_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(SWOTCategory::class, 'category_id');
        }
        
}