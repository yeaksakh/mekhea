<?php

namespace Modules\ProductCostingB11\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductCostingB11 extends Model
{
    protected $fillable = [
        'business_id',
        'category_id',
        'product_1',
        'cost_2',
        'qty_3',
        'created_by'
    ];

    protected $table = 'productcostingb11_main'; // Specify the table name

    public function productcost()
    {
        return $this->hasMany(ProductCost::class, 'product_cost_id');
    }
}
