<?php

namespace Modules\AuditExpense\Entities;

use Illuminate\Database\Eloquent\Model;

class AuditExpenseSocial extends Model
{
    protected $guarded = ['*']; // Protect all fields
    protected $table = 'auditexpense_socials';
    public $fillable = ['business_id','social_type', 'social_id', 'social_token', 'social_status'];
}