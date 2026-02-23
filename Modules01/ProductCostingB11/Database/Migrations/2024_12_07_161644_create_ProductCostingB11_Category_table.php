<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCostingB11CategoryTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('productcostingb11_category')) {
            Schema::create('productcostingb11_category', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('business_id');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ProductCostingB11Category');
    }
}
