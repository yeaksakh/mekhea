<?php

namespace Modules\DocumentKeeper\Entities;

use Illuminate\Database\Eloquent\Model;

class DocumentKeeper extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'documentkeeper_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(DocumentKeeperCategory::class, 'category_id');
        }
        
}