<?php

namespace Modules\EmployeeTracker\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeTrackerCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'employeetracker_main'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function employeetracker()
    {
        return $this->hasMany(EmployeeTracker::class, 'category_id');
    }
}