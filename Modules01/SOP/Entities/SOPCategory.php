<?php

namespace Modules\SOP\Entities;

use Illuminate\Database\Eloquent\Model;

class SOPCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'sop_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function sop()
    {
        return $this->hasMany(SOP::class, 'category_id');
    }
}