<?php

namespace Modules\PurchaseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchaseAutoFill extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'purchaseautofill_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(PurchaseAutoFillCategory::class, 'category_id');
        }
        
}