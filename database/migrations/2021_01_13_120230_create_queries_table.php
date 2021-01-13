<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->integer('clientId');
            $table->string('type');
            $table->date('travelDate')->nullable();
            $table->string('destination')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('kids')->nullable();
            $table->integer('rooms')->nullable();
            $table->longText('remarks')->nullable();
            $table->boolean('mailSent')->default(0);
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
        Schema::dropIfExists('queries');
    }
}
