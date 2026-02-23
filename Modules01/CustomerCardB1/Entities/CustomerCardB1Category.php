<?php

namespace Modules\CustomerCardB1\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomerCardB1Category extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'customercardb1_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function customercardb1()
    {
        return $this->hasMany(CustomerCardB1::class, 'category_id');
    }
}