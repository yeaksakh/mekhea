<?php

namespace Modules\ExpenseRequest\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseRequest extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'expenserequest_main'; // Specify the table name

    
                                public function whorequestexpense3()
                            {
                                return $this->belongsTo(\App\User::class, 'who_request_expense_3');
                            }
                        

        public function category()
        {
            return $this->belongsTo(ExpenseRequestCategory::class, 'category_id');
        }
        
}