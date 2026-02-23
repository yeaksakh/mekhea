<?php

namespace Modules\ProductBook\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductBookCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'productbook_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function productbook()
    {
        return $this->hasMany(ProductBook::class, 'category_id');
    }
}