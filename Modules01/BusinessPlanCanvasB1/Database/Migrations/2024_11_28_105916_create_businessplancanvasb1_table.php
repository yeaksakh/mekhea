<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessPlanCanvasB1Table extends Migration
{
    public function up()
    {
        Schema::create('businessplancanvasb1_main', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('business_id');
            $table->text('description')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            
            
            
            
            
            $table->text('CustomerSegments_1')->nullable();
$table->text('ValuePropositions_2')->nullable();
$table->text('Channels_3')->nullable();
$table->text('CustomerRelationships_4')->nullable();
$table->text('ReveneuStreams_5')->nullable();
$table->text('KeyResources_6')->nullable();
$table->text('KeyActivities_7')->nullable();
$table->text('KeyPartner_8')->nullable();
$table->text('CostStructure_9')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businessplancanvasb1_main');
    }
}