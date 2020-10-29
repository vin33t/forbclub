<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_holidays', function (Blueprint $table) {
            $table->id();
          $table->integer('client_id');
          $table->integer('converted_by');
          $table->integer('bookings_id');
          $table->string('holiday_type');
          $table->string('destination');
          $table->date('date_of_travel');
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
        Schema::dropIfExists('client_holidays');
    }
}
