<?php

namespace Modules\BotTelegramManager\Entities;

use Illuminate\Database\Eloquent\Model;

class BotTelegramManager extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'bottelegrammanager_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(BotTelegramManagerCategory::class, 'category_id');
        }
        
}