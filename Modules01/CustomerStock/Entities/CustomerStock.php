<?php

namespace Modules\CustomerStock\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerStock extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $fillable = [
    'delivery_id',
    'business_id',
    'created_by',
    'customer_id',
    'invoice_id',
    'product_id',
    'qty_reserved',
    'qty_delivered',
    'qty_remaining',
    'status',
    'dilivery_date'
];

    protected $table = 'customerstock_main'; // Specify the table name
  
}