<?php

namespace Modules\AutoAudit\Entities;

use Illuminate\Database\Eloquent\Model;

class AutoAuditSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'autoaudit_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}