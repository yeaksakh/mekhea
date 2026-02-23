<?php

namespace Modules\EmployeeCardB1\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeCardB1 extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'employeecardb1_main'; // Specify the table name

    
                                public function employee1()
                            {
                                return $this->belongsTo(\App\User::class, 'employee_1');
                            }
                        

        public function category()
        {
            return $this->belongsTo(EmployeeCardB1Category::class, 'category_id');
        }
        
}