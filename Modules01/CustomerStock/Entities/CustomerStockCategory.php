<?php

namespace Modules\CustomerStock\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerStockCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'customerstock_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function customerstock()
    {
        return $this->hasMany(CustomerStock::class, 'category_id');
    }
}