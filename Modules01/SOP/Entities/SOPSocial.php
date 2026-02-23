<?php

namespace Modules\SOP\Entities;

use Illuminate\Database\Eloquent\Model;

class SOPSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'sop_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}