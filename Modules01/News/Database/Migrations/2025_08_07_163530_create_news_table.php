<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('news_main')) {
            Schema::create('news_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->string('title_1')->nullable();
$table->string('image_5')->nullable();
$table->longtext('description_6')->nullable();
$table->string('image_2_7')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('news_socials')) {
            Schema::create('news_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('news_socials');
    }
}