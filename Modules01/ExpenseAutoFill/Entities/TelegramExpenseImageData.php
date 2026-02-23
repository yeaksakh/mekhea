<?php

namespace Modules\ExpenseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class TelegramExpenseImageData extends Model
{
    protected $fillable = [
        'business_id',
        'telegram_file_id',
        'telegram_file_unique_id',
        'telegram_file_size',
        'telegram_file_name',
        'telegram_width',
        'telegram_height',
        'telegram_user_id',
        'telegram_user_first_name',
        'telegram_user_last_name',
        'telegram_user_username',
        'telegram_user_photo_url',
        'telegram_date',
        'telegram_message_id',
        'telegram_chat_id',
        'file_path',
        'status',
        'total_amount',
        'transaction_date',
        'supplier',
        'location',
        'category',
        'sub_category',
        'tax',
        'expense_for',
        'ref_no',
        'notes',
        'employee_name'
    ];

    protected $casts = [
        'telegram_date' => 'datetime',
    ];
}
