<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeetrackerFormFieldsAndSocialsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employeetracker_form_fields')) {
            Schema::create('employeetracker_form_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('form_id');
                $table->string('field_label');
                $table->string('field_type');
                $table->unsignedInteger('field_order');
                $table->boolean('is_required')->default(false);
                $table->json('config')->nullable();
                $table->timestamps();
                
                $table->foreign('form_id')->references('id')->on('employeetracker_main')->onDelete('cascade');
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
        // Drop dependent table employeetracker_activities first
        Schema::dropIfExists('employeetracker_activities');
        
        // Then drop employeetracker_form_fields
        Schema::table('employeetracker_form_fields', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
        });
        Schema::dropIfExists('employeetracker_form_fields');
        
        // Finally drop employeetracker_socials
        Schema::dropIfExists('employeetracker_socials');
    }
}