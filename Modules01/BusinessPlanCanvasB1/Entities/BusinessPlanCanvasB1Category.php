<?php

namespace Modules\BusinessPlanCanvasB1\Entities;

use Illuminate\Database\Eloquent\Model;

class BusinessPlanCanvasB1Category extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'businessplancanvasb1_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
}