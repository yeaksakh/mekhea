<?php

namespace Modules\ProductCostingB11\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductCostingB11Category extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'productcostingb11_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
}