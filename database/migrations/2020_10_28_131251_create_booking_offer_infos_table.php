<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingOfferInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_offer_infos', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('booking_offer_id');

          $table->string('vendor_name');
          $table->string('service_type');
          $table->string('destination')->nullable();

          $table->integer('nights')->nullable();
          $table->string('hotel_name')->nullable();
          $table->Date('check_in')->nullable();
          $table->Date('check_out')->nullable();
          $table->integer('pax')->nullable();

          $table->integer('flight_pax')->nullable();
          $table->string('flight_details')->nullable();

          $table->longText('remarks')->nullable();

          $table->integer('vendor_price')->nullable();
          $table->integer('our_price')->nullable();

          $table->boolean('add_on')->default(0);
          $table->boolean('add_more')->default(0);


          $table->integer('add_on_service_price')->nullable();
          $table->integer('amount_paid_by_client')->nullable();
          $table->integer('cabin_baggage')->nullable();
          $table->integer('check_in_baggage')->nullable();
          $table->integer('check_in_baggage_price')->nullable();
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
        Schema::dropIfExists('booking_offer_infos');
    }
}
