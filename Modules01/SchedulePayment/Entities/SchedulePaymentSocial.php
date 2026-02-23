<?php

namespace Modules\SchedulePayment\Entities;

use Illuminate\Database\Eloquent\Model;

class SchedulePaymentSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'schedulepayment_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}