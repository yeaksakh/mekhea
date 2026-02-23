<?php

namespace Modules\Announcement\Entities;

use Illuminate\Database\Eloquent\Model;

class AnnouncementSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'announcement_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}