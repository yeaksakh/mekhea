<?php

namespace Modules\ProductCostingB11\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_cost_id',
        'name',
        'value',
        'qty'
    ];
    protected $table = 'product_costs';

    /**
     * Relationship: Competency belongs to an indicator.
     */
    public function productcostingb11()
    {
        return $this->belongsTo(ProductCostingB11::class, 'product_cost_id');
    }
}
