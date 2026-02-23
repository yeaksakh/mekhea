<?php

namespace Modules\AutoAudit\Entities;

use Illuminate\Database\Eloquent\Model;

class AutoAudit extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'autoaudit_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(AutoAuditCategory::class, 'category_id');
        }
        
}