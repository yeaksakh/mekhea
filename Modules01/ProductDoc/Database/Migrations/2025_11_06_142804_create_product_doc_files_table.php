<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDocFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('productdoc_files')) {

            Schema::create('productdoc_files', function (Blueprint $table) {
                $table->id();
                 $table->softDeletes();

                // Foreign keys
                $table->unsignedBigInteger('business_id');
                $table->unsignedBigInteger('social_id');

                // File information from Telegram
                $table->string('file_type'); // video, video_note, document, photo
                $table->string('file_id');
                $table->string('file_unique_id');
                $table->bigInteger('file_size')->nullable();
                $table->string('file_name')->nullable();
                $table->string('mime_type')->nullable();

                // Video specific fields
                $table->integer('duration')->nullable(); // For videos in seconds
                $table->integer('width')->nullable(); // For videos and photos
                $table->integer('height')->nullable(); // For videos and photos
                $table->integer('length')->nullable(); // For video notes (dimension)

                // Thumbnail information
                $table->json('thumbnail')->nullable(); // Original thumbnail data from Telegram
                $table->string('thumbnail_path')->nullable(); // Path to downloaded thumbnail

                // User information
                $table->bigInteger('from_user_id');
                $table->string('from_user_name');
                $table->string('from_user_username')->nullable();

                // Message information
                $table->integer('message_id');
                $table->timestamp('message_date');

                // Local storage
                $table->string('local_path')->nullable(); // Path to downloaded file

                // Status
                $table->string('status')->default('pending'); // pending, processing, completed, failed

                // Timestamps
                $table->timestamps();

                // Indexes for better performance
                $table->index(['business_id', 'file_type']);
                $table->index(['file_unique_id']);
                $table->index(['from_user_id']);
                $table->index(['message_date']);
                $table->index(['status']);

                // Unique constraint to prevent duplicates
                $table->unique(['business_id', 'file_unique_id']);
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
        Schema::dropIfExists('productdoc_files');
    }
}
