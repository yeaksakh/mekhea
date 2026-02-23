<?php

namespace Modules\ExpenseAutoFill\Entities;

use Illuminate\Database\Eloquent\Model;

class ExpenseAutoFillCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'expenseautofill_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function expenseautofill()
    {
        return $this->hasMany(ExpenseAutoFill::class, 'category_id');
    }
}