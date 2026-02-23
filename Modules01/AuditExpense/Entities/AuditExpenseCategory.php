<?php

namespace Modules\AuditExpense\Entities;

use Illuminate\Database\Eloquent\Model;

class AuditExpenseCategory extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'auditexpense_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
    public function auditexpense()
    {
        return $this->hasMany(AuditExpense::class, 'category_id');
    }
}