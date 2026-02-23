<?php

namespace Modules\DocumentKeeper\Entities;

use Illuminate\Database\Eloquent\Model;

class DocumentKeeperCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'documentkeeper_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function documentkeeper()
    {
        return $this->hasMany(DocumentKeeper::class, 'category_id');
    }
}