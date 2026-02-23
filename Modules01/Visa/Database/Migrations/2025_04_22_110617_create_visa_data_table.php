<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('visa_indicators')) {
            Schema::create('visa_indicators', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->unsignedBigInteger('department_id'); // No foreign key constraint
                $table->unsignedBigInteger('designation_id')->nullable(); // No foreign key constraint
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visa_competencies')) {
            Schema::create('visa_competencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('indicator_id')->constrained('visa_indicators')->onDelete('cascade');
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->string('type'); 
                $table->string('name');
                $table->string('value')->nullable();;
                $table->string('score')->nullable();;
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visa_appraisals')) {
            Schema::create('visa_appraisals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('indicator_id');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->unsignedBigInteger('contact_id')->nullable(); 
                $table->unsignedBigInteger('business_id'); // No foreign key constraint
                $table->string('appraisal_month', 7); // YYYY-MM format
                $table->unsignedBigInteger('created_by',);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visa_appraisal_scores')) {
            Schema::create('visa_appraisal_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appraisal_id')->constrained('visa_appraisals')->onDelete('cascade');
                $table->foreignId('competency_id')->constrained('visa_competencies')->onDelete('cascade');
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
        Schema::dropIfExists('visa_appraisal_scores');
        Schema::dropIfExists('visa_appraisals');
        Schema::dropIfExists('visa_competencies');
        Schema::dropIfExists('visa_indicators');
    }
};
