<?php

namespace Modules\News\Entities;

use Illuminate\Database\Eloquent\Model;

class NewsSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'news_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}