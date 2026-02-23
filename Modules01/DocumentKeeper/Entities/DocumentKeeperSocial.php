<?php

namespace Modules\DocumentKeeper\Entities;

use Illuminate\Database\Eloquent\Model;

class DocumentKeeperSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'documentkeeper_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}