<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeetrackerMainTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employeetracker_main')) {
            Schema::create('employeetracker_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('business_id');
                // Changed from unsignedBigInteger to unsignedInteger to match users.id
                $table->unsignedInteger('created_by');
                $table->string('department');
                $table->string('name');
                $table->text('description')->nullable();                                                                                                                         
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employeetracker_main');
    }
}