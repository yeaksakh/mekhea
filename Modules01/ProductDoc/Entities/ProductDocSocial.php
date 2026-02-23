<?php

namespace Modules\ProductDoc\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductDocSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'productdoc_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}