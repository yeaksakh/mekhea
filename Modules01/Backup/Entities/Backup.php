<?php

namespace Modules\Backup\Entities;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'backup_main'; // Specify the table name


    public function name1()
    {
        return $this->belongsTo(\App\User::class, 'name_1');
    }


    public function category()
    {
        return $this->belongsTo(BackupCategory::class, 'category_id');
    }
}
