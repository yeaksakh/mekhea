<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentaryTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('documentary_main')) {
            Schema::create('documentary_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                $table->string('title_1')->nullable();
                $table->string('url_5')->nullable();
                $table->string('file_6')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('documentary_socials')) {
            Schema::create('documentary_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('documentary_socials');
    }
}