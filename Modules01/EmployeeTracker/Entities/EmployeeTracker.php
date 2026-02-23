<?php

namespace Modules\EmployeeTracker\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeTracker extends Model
{
    protected $table = 'employeetracker_main';

    protected $fillable = [
        'business_id',
        'created_by',
        'department',
        'name',
        'description',
        'is_active',
    ];

    public function form()
    {
        return $this->belongsTo(EmployeeTrackerMain::class, 'form_id');
    }
    
    public function fields()
    {
        return $this->hasMany(EmployeeTrackerFormField::class, 'form_id')->orderBy('field_order');
    }
    

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(\App\Category::class, 'department');
    }

    // Relationships for original EmployeeTracker functionality
    public function department1()
    {
        return $this->belongsTo(\App\Category::class, 'department_1');
    }

    public function employee2()
    {
        return $this->belongsTo(\App\User::class, 'employee_2');
    }
}
