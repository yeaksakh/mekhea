<?php
namespace Modules\ExpenseAutoFill\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramOcrData extends Model
{
    use HasFactory;
    
    protected $fillable = [
    'business_id',
    'telegram_file_id',
    'telegram_file_unique_id',
    'telegram_file_size',
    'telegram_width',
    'telegram_height',
    'telegram_from',
    'telegram_date',
    'telegram_message_id',
    'telegram_chat_id',
    'image_path',
    'contact_id',
    'supplier_name',
    'company_name',
    'ref_no',
    'transaction_date',
    'status',
    'location_id',
    'exchange_rate',
    'pay_term_number',
    'pay_term_type',
    'document',
    'custom_field_1',
    'custom_field_2',
    'custom_field_3',
    'custom_field_4',
    'purchase_order_ids',
    'product',
    'discount_type',
    'discount_amount',
    'tax_id',
    'tax_amount',
    'additional_notes',
    'shipping_details',
    'shipping_charges',
    'shipping_custom_field_1',
    'shipping_custom_field_2',
    'shipping_custom_field_3',
    'shipping_custom_field_4',
    'shipping_custom_field_5',
    'additional_expense_key_1',
    'additional_expense_value_1',
    'additional_expense_key_2',
    'additional_expense_value_2',
    'additional_expense_key_3',
    'additional_expense_value_3',
    'additional_expense_key_4',
    'additional_expense_value_4',
    'final_total',
    'advance_balance',
    'ocr_status',
    'ocr_error'
];
    
    protected $casts = [
        'telegram_date' => 'datetime',
        'transaction_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'additional_expense_value_1' => 'decimal:2',
        'additional_expense_value_2' => 'decimal:2',
        'additional_expense_value_3' => 'decimal:2',
        'additional_expense_value_4' => 'decimal:2',
        'final_total' => 'decimal:2',
        'advance_balance' => 'decimal:2',
    ];
}