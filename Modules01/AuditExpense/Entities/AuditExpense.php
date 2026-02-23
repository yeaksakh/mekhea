<?php

namespace Modules\AuditExpense\Entities;

use Illuminate\Database\Eloquent\Model;

class AuditExpense extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'auditexpense_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(AuditExpenseCategory::class, 'category_id');
        }
        
}