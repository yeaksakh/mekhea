<?php

namespace Modules\BotTelegramManager\Entities;

use Illuminate\Database\Eloquent\Model;

class BotTelegramManagerSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'bottelegrammanager_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}