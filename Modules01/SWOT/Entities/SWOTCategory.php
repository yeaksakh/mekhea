<?php

namespace Modules\SWOT\Entities;

use Illuminate\Database\Eloquent\Model;

class SWOTCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'swot_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function swot()
    {
        return $this->hasMany(SWOT::class, 'category_id');
    }
}