<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseAutoFillTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('expenseautofill_socials')) {
            Schema::create('expenseautofill_socials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->string('social_type')->nullable();
                $table->string('social_id')->nullable();
                $table->string('social_token')->nullable();
                $table->boolean('social_status')->default(0)->nullable();
                $table->longText('prompt')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('expenseautofill_socials');
    }
}