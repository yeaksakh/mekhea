<?php

namespace Modules\SchedulePayment\Entities;

use Illuminate\Database\Eloquent\Model;

class SchedulePayment extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'schedulepayment_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(SchedulePaymentCategory::class, 'category_id');
        }
        
}