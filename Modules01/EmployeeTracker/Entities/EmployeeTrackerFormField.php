<?php

namespace Modules\EmployeeTracker\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeTrackerFormField extends Model
{
    protected $table = 'employeetracker_form_fields';

    protected $fillable = [
        'form_id',
        'field_label',
        'field_type',
        'field_order',
        'is_required',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_required' => 'boolean',
    ];

     public function form()
    {
        return $this->belongsTo(EmployeeTracker::class, 'form_id');
    }
}
