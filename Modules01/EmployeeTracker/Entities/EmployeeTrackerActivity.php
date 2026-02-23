<?php

namespace Modules\EmployeeTracker\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class EmployeeTrackerActivity extends Model
{
    protected $table = 'employeetracker_activities';

    protected $fillable = [
        'form_id',
        'field_id',
        'user_id',
        'value',
    ];

    public function form()
    {
        return $this->belongsTo(EmployeeTracker::class, 'form_id');
    }

    public function field()
    {
        return $this->belongsTo(EmployeeTrackerFormField::class, 'field_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}