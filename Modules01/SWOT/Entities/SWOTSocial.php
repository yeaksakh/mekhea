<?php

namespace Modules\SWOT\Entities;

use Illuminate\Database\Eloquent\Model;

class SWOTSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'swot_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}