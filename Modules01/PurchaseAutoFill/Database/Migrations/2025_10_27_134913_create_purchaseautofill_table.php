<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseAutoFillTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('purchaseautofill_main')) {
            Schema::create('purchaseautofill_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->string('title_1')->nullable();
$table->string('topic _5')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchaseautofill_socials')) {
            Schema::create('purchaseautofill_socials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->string('social_type')->nullable();
                $table->string('social_id')->nullable();
                $table->string('social_token')->nullable();
                $table->boolean('social_status')->default(0)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('purchaseautofill_socials');
    }
}