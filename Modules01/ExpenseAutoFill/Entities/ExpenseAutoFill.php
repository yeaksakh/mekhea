<?php

namespace Modules\ExpenseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseAutoFill extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'expenseautofill_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(ExpenseAutoFillCategory::class, 'category_id');
        }
        
}

