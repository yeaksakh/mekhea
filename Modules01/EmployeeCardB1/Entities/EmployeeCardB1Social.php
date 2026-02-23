<?php

namespace Modules\EmployeeCardB1\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeCardB1Social extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'employeecardb1_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}