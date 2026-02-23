<?php

namespace Modules\BotTelegramManager\Entities;

use Illuminate\Database\Eloquent\Model;

class BotTelegramManagerCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'bottelegrammanager_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function bottelegrammanager()
    {
        return $this->hasMany(BotTelegramManager::class, 'category_id');
    }
}