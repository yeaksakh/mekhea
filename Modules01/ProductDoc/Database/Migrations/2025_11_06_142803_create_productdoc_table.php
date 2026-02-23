<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDocTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('productdoc_main')) {
            Schema::create('productdoc_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->unsignedBigInteger('Product_1')->nullable();
$table->string('productFile1_5')->nullable();
$table->string('productFile2_6')->nullable();
$table->string('productFile3_7')->nullable();
$table->string('productFile4_8')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('productdoc_socials')) {
            Schema::create('productdoc_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('productdoc_socials');
    }
}