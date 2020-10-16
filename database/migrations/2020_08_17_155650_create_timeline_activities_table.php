<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimelineActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('user_id');
            $table->string('title');
            $table->longText('body');
            $table->string('model_type')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timeline_activities');
    }
}
