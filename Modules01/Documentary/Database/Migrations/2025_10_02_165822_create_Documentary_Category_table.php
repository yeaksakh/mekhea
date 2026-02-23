<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentaryCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('documentary_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(); // Added parent_id field
            $table->unsignedBigInteger('business_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraint with ON DELETE SET NULL
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('documentary_category')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentary_category');
    }
}
