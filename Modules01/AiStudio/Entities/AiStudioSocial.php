<?php

namespace Modules\AiStudio\Entities;

use Illuminate\Database\Eloquent\Model;

class AiStudioSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'aistudio_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}