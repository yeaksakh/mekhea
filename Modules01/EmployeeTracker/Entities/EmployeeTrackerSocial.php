<?php

namespace Modules\EmployeeTracker\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeTrackerSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'employeetracker_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}