<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseAutoFillCategoryTable extends Migration
{
    public function up()
    {
          if (!Schema::hasTable('purchaseautofill_category')) {
        Schema::create('purchaseautofill_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
}

    public function down()
    {
        Schema::dropIfExists('PurchaseAutoFillCategory');
    }
}