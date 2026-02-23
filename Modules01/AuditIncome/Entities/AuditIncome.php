<?php

namespace Modules\AuditIncome\Entities;

use Illuminate\Database\Eloquent\Model;

class AuditIncome extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'auditincome_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(AuditIncomeCategory::class, 'category_id');
        }
        
}