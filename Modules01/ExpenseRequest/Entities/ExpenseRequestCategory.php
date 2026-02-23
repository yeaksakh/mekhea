<?php

namespace Modules\ExpenseRequest\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseRequestCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'expenserequest_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function expenserequest()
    {
        return $this->hasMany(ExpenseRequest::class, 'category_id');
    }
}