<?php

namespace Modules\AiStudio\Entities;

use Illuminate\Database\Eloquent\Model;

class AiStudio extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'aistudio_main'; // Specify the table name

    
        public function category()
        {
            return $this->belongsTo(AiStudioCategory::class, 'category_id');
        }
        
}