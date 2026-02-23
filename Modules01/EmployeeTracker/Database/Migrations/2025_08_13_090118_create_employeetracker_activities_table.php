<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeetrackerActivitiesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employeetracker_activities')) {
            Schema::create('employeetracker_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('form_id');
                $table->unsignedBigInteger('field_id');
                // Changed from unsignedInteger to unsignedInteger to match users.id
                $table->unsignedInteger('user_id');
                $table->text('value');
                $table->timestamps();

                $table->foreign('form_id')->references('id')->on('employeetracker_main')->onDelete('cascade');
                $table->foreign('field_id')->references('id')->on('employeetracker_form_fields')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('employeetracker_activities', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropForeign(['field_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('employeetracker_activities');
    }
}