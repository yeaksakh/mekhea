<?php

namespace Modules\AiStudio\Entities;

use Illuminate\Database\Eloquent\Model;

class AiStudioCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'aistudio_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function aistudio()
    {
        return $this->hasMany(AiStudio::class, 'category_id');
    }
}