<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramOcrDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('telegram_ocr_data')) {

            Schema::create('telegram_ocr_data', function (Blueprint $table) {
                $table->id();

                // Business association
                $table->integer('business_id')->unsigned();
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

                // Telegram specific fields
                $table->string('telegram_file_id', 255);
                $table->string('telegram_file_unique_id', 255);
                $table->integer('telegram_file_size');
                $table->integer('telegram_width');
                $table->integer('telegram_height');
                $table->string('telegram_from', 255);
                $table->timestamp('telegram_date');
                $table->string('telegram_message_id', 50);
                $table->string('telegram_chat_id', 50)->nullable();
                $table->string('image_path', 255);

                // OCR Data Fields
                $table->integer('contact_id')->nullable();
                $table->string('supplier_name', 255)->nullable(); // Added
                $table->string('company_name', 255)->nullable();  // Added
                $table->string('ref_no', 255)->nullable();
                $table->date('transaction_date')->nullable();
                $table->string('status', 50)->nullable();
                $table->integer('location_id')->nullable();
                $table->decimal('exchange_rate', 10, 2)->nullable();
                $table->integer('pay_term_number')->nullable();
                $table->string('pay_term_type', 50)->nullable();
                $table->string('document', 255)->nullable();
                $table->string('custom_field_1', 255)->nullable();
                $table->string('custom_field_2', 255)->nullable();
                $table->string('custom_field_3', 255)->nullable();
                $table->string('custom_field_4', 255)->nullable();
                $table->text('purchase_order_ids')->nullable();
                $table->json('product')->nullable();
                $table->string('discount_type', 50)->nullable();
                $table->decimal('discount_amount', 10, 2)->nullable();
                $table->integer('tax_id')->nullable();
                $table->decimal('tax_amount', 10, 2)->nullable();
                $table->text('additional_notes')->nullable();
                $table->text('shipping_details')->nullable();
                $table->decimal('shipping_charges', 10, 2)->nullable();
                $table->string('shipping_custom_field_1', 255)->nullable();
                $table->string('shipping_custom_field_2', 255)->nullable();
                $table->string('shipping_custom_field_3', 255)->nullable();
                $table->string('shipping_custom_field_4', 255)->nullable();
                $table->string('shipping_custom_field_5', 255)->nullable();
                $table->string('additional_expense_key_1', 255)->nullable();
                $table->decimal('additional_expense_value_1', 10, 2)->nullable();
                $table->string('additional_expense_key_2', 255)->nullable();
                $table->decimal('additional_expense_value_2', 10, 2)->nullable();
                $table->string('additional_expense_key_3', 255)->nullable();
                $table->decimal('additional_expense_value_3', 10, 2)->nullable();
                $table->string('additional_expense_key_4', 255)->nullable();
                $table->decimal('additional_expense_value_4', 10, 2)->nullable();
                $table->decimal('final_total', 10, 2)->nullable();
                $table->decimal('advance_balance', 10, 2)->nullable();

                // Processing status
                $table->enum('ocr_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->text('ocr_error')->nullable();
                $table->json('ocr_data')->nullable(); // Added

                // Timestamps
                $table->timestamps();

                // Indexes
                $table->index('telegram_file_id');
                $table->index('ocr_status');
                $table->index('transaction_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_ocr_data');
    }
}
