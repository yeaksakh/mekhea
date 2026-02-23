<?php

namespace Modules\ExpenseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseAutoFillSocial extends Model
{
    protected $table = 'expenseautofill_socials';

    // Remove $guarded = ['*'] OR use $fillable, not both
    protected $fillable = [
        'business_id', 'social_type', 'social_id', 
        'social_token', 'social_status', 'prompt'
    ];

    // Correct accessor name (snake_case → StudlyCase)
    public function getTokenKeyAttribute()
    {
        return $this->social_token;
    }
}