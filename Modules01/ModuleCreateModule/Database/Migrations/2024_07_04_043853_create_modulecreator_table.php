<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('modulecreator')) {
            Schema::create('modulecreator', function (Blueprint $table) {
                $table->id();
                $table->string('module_name')->unique();
                $table->boolean('enabled_modules')->default(0);
                $table->unsignedBigInteger('business_id');
                $table->string('icon')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('modulecreator');
    }
};
