<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTrackerTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employeetracker_main')) {
            Schema::create('employeetracker_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->unsignedBigInteger('department_1')->nullable();
$table->unsignedBigInteger('employee_2')->nullable();
$table->string('title_3')->nullable();
$table->longtext('description_4')->nullable();
$table->string('file_5')->nullable();
$table->string('image_6')->nullable();
$table->boolean('status_7')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('employeetracker_socials')) {
            Schema::create('employeetracker_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('employeetracker_socials');
    }
}