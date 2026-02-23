<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCostingB11Table extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('productcostingb11_main')) {
            Schema::create('productcostingb11_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                $table->string('product_1')->nullable();
                $table->unsignedInteger('cost_2')->nullable();
                $table->unsignedInteger('qty_3')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('productcostingb11_main');
    }
}
