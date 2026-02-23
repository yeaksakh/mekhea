<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulePaymentTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('schedulepayment_main')) {
            Schema::create('schedulepayment_main', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->unsignedInteger('created_by')->nullable();
                
                
                
                
                
                
                
                $table->string('title_1')->nullable();
$table->date('date_paid_5')->nullable();
$table->date('date_prepare_pay_6')->nullable();
$table->boolean('status_7')->nullable();
$table->longtext('note_8')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('schedulepayment_socials')) {
            Schema::create('schedulepayment_socials', function (Blueprint $table) {
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
        Schema::dropIfExists('schedulepayment_socials');
    }
}