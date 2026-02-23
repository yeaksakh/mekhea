<?php

namespace Modules\ProductDoc\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductDocCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'productdoc_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function productdoc()
    {
        return $this->hasMany(ProductDoc::class, 'category_id');
    }
}