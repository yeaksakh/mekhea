<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSWOTTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('swot_main')) {
            Schema::create('swot_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->string('Title_1')->nullable();
$table->longtext('Strengths_5')->nullable();
$table->longtext('Weaknesses_6')->nullable();
$table->longtext('Opportunities_7')->nullable();
$table->longtext('Threats_8')->nullable();
$table->longtext('Note_9')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('swot_socials')) {
            Schema::create('swot_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('swot_socials');
    }
}