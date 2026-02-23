<?php

namespace Modules\CustomerCardB1\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerCardB1 extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'customercardb1_main'; // Specify the table name

    
                                public function customer1()
                            {
                                return $this->belongsTo(\App\Contact::class, 'customer_1');
                            }
                        

        public function category()
        {
            return $this->belongsTo(CustomerCardB1Category::class, 'category_id');
        }
        
}