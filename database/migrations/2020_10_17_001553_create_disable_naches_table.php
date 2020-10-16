<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisableNachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disable_naches', function (Blueprint $table) {
            $table->id();
          $table->integer('client_id');
          $table->string('month')->nullable();
          $table->string('year')->nullable();
          $table->longText('remarks')->nullable();
          $table->boolean('permanent')->default(0);
          $table->string('bank');
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
        Schema::dropIfExists('disable_naches');
    }
}
