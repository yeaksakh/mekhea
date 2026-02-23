<?php

namespace Modules\Backup\Entities;

use Illuminate\Database\Eloquent\Model;

class BackupSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'backup_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}