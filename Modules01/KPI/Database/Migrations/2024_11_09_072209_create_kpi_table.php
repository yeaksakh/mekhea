<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kpi_indicators')) {
            Schema::create('kpi_indicators', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->unsignedBigInteger('department_id'); // No foreign key constraint
                $table->unsignedBigInteger('designation_id')->nullable(); // No foreign key constraint
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kpi_competencies')) {
            Schema::create('kpi_competencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('indicator_id')->constrained('kpi_indicators')->onDelete('cascade');
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->string('type'); 
                $table->string('name');
                $table->string('value')->nullable();;
                $table->string('score')->nullable();;
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kpi_appraisals')) {
            Schema::create('kpi_appraisals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('indicator_id');
                $table->unsignedBigInteger('employee_id')->nullable(); // Employee can be null
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->string('appraisal_month', 7); // YYYY-MM format
                $table->unsignedBigInteger('created_by',);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kpi_appraisal_scores')) {
            Schema::create('kpi_appraisal_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appraisal_id')->constrained('kpi_appraisals')->onDelete('cascade');
                $table->foreignId('competency_id')->constrained('kpi_competencies')->onDelete('cascade');
                $table->unsignedBigInteger('business_id');
                $table->string('expect_value')->nullable();
                $table->string('expect_score')->nullable();
                $table->string('actual_value')->nullable();
                $table->string('actual_score')->nullable();
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('kpi_appraisal_scores');
        Schema::dropIfExists('kpi_appraisals');
        Schema::dropIfExists('kpi_competencies');
        Schema::dropIfExists('kpi_indicators');
    }
};
