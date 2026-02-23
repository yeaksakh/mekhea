<?php

namespace Modules\CustomerStock\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerStockSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'customerstock_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}