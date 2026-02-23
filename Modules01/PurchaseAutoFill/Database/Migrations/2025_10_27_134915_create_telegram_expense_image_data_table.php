<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramExpenseImageDataTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('telegram_expense_image_data')) {
            Schema::create('telegram_expense_image_data', function (Blueprint $table) {
                $table->id();
                $table->integer('business_id');
                $table->string('telegram_file_id');
                $table->string('telegram_file_unique_id');
                $table->integer('telegram_file_size')->nullable();
                $table->string('telegram_file_name')->nullable();
                $table->integer('telegram_width')->nullable();
                $table->integer('telegram_height')->nullable();
                $table->bigInteger('telegram_user_id');
                $table->string('telegram_user_first_name');
                $table->string('telegram_user_last_name')->nullable();
                $table->string('telegram_user_username')->nullable();
                $table->string('telegram_user_photo_url')->nullable();
                $table->dateTime('telegram_date');
                $table->integer('telegram_message_id');
                $table->bigInteger('telegram_chat_id');
                $table->string('file_path');
                $table->string('status')->default('stored');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('telegram_expense_image_data');
    }
}
