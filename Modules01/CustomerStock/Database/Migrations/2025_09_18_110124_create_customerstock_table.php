<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerStockTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customerstock_main')) {
            Schema::create('customerstock_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                 $table->unsignedBigInteger('delivery_id');
                $table->unsignedInteger('customer_id');
                $table->unsignedInteger('invoice_id');
                $table->unsignedInteger('product_id');
                $table->float('qty_reserved');
                $table->float('qty_delivered');
                $table->float('qty_remaining');
                $table->string('status');
                $table->unsignedInteger('created_by');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customerstock_main');
    }
}