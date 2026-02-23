<?php

namespace Modules\ProductDoc\Entities;

use App\DynamicField;
use Illuminate\Database\Eloquent\Model;

class ProductDoc extends Model
{
   

    protected $table = 'productdoc_main'; // Specify the table name
    protected $fillable = [
        'business_id',
        'category_id',
        'created_by',
        'Product_1',
        'productFile1_5',
        'productFile2_6',
        'productFile3_7',
        'productFile4_8',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->addDynamicFieldsToFillable();
    }

    /**
     * Dynamically add fields to fillable based on database_fields table
     */
    protected function addDynamicFieldsToFillable()
    {
        $dynamicFields = DynamicField::where('table_name', $this->table)
            ->where('is_active', true)
            ->pluck('field_name')
            ->toArray();

        $this->fillable = array_merge($this->fillable, $dynamicFields);
    }

    /**
     * Get all dynamic fields for this table
     */
    public static function getDynamicFields()
    {
        return DynamicField::where('table_name', 'productdoc_main')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get dynamic fields as array (field_name => field_label)
     */
    public static function getDynamicFieldsArray()
    {
        return DynamicField::where('table_name', 'productdoc_main')
            ->where('is_active', true)
            ->pluck('field_label', 'field_name')
            ->toArray();
    }
    public function hasAnyFile(): bool
    {
        foreach ($this->getAttributes() as $key => $value) {
            if (preg_match('/^productFile\d+_\d+$/', $key) && !empty($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get validation rules including dynamic fields
     */
    public static function getDynamicValidationRules()
    {
        $fields = DynamicField::where('table_name', 'productdoc_main')
            ->where('is_active', true)
            ->get();

        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->field_rules) {
                $customRules = explode('|', $field->field_rules);
                $fieldRules = array_merge($fieldRules, $customRules);
            }

            $rules[$field->field_name] = implode('|', $fieldRules);
        }

        return $rules;
    }

    
    public function Product1()
    {
        return $this->belongsTo(\App\Product::class, 'Product_1');
    }
                        
    public function category()
    {
        return $this->belongsTo(ProductDocCategory::class, 'category_id');
    }
        
}