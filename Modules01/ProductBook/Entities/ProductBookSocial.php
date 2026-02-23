<?php

namespace Modules\ProductBook\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductBookSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'productbook_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}