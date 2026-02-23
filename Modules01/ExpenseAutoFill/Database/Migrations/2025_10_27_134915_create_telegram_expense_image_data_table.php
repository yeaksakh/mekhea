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

                // New fields
                $table->float('total_amount')->nullable();                    // {"value": numeric, "currency": "USD" or null}
                $table->string('transaction_date')->nullable();              // DD/MM/YYYY or null
                $table->string('supplier')->nullable();
                $table->string('location')->nullable();
                $table->string('category')->nullable();
                $table->string('sub_category')->nullable();
                $table->string('tax')->nullable();
                $table->string('expense_for')->nullable();
                $table->string('ref_no')->nullable();
                $table->text('notes')->nullable();
                $table->text('employee_name')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('telegram_expense_image_data');
    }
}
