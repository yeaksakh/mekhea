<?php

namespace Modules\Products\Entities; // Or Modules\Products\Entities if in a module

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    protected $casts = [
        'sub_unit_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}