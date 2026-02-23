<?php

namespace Modules\PurchaseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchaseAutoFillSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'purchaseautofill_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}