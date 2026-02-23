<?php

namespace Modules\Backup\Entities;

use Illuminate\Database\Eloquent\Model;

class BackupCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'backup_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function backup()
    {
        return $this->hasMany(Backup::class, 'category_id');
    }
}