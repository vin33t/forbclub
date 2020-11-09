<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientHolidayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_holiday_details', function (Blueprint $table) {
            $table->id();
          $table->integer('client_holiday_id');

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

          $table->boolean('add_on')->default(0);

          $table->integer('vendor_price')->nullable();
          $table->integer('our_price')->nullable();

          $table->integer('add_on_service_price')->nullable();
          $table->integer('amount_paid_by_client')->nullable();
          $table->boolean('cancelled')->default(0);
          $table->date('cancelled_on')->nullable();
          $table->integer('cancelled_by')->nullable();

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
        Schema::dropIfExists('client_holiday_details');
    }
}
