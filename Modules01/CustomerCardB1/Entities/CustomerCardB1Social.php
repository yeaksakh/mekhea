<?php

namespace Modules\CustomerCardB1\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerCardB1Social extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'customercardb1_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}