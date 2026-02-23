<?php

namespace Modules\ExpenseRequest\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseRequestSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'expenserequest_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}