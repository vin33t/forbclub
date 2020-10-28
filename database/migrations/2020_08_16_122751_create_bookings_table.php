<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//      $b->client_id = $client->id;
//      $b->booking_request_date = $request->booking_request_date;
//      $b->travel_date = $request->travel_date;
//      $b->total_nights = $request->total_nights;
//      $b->holiday_type = $request->holiday_type;
//      $b->eligible = $request->eligible;
//      $b->breakfast = $request->breakfast;
//      $b->remarks = $request->remarks;
//      $b->added_by = Auth::id();
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('clientId');
            $table->date('bookingRequestDate');
            $table->date('travelDate');
            $table->string('totalNights');
            $table->string('holidayType');
            $table->string('eligible');
            $table->string('breakfast');
            $table->longText('remarks');
            $table->string('status')->nullable();
            $table->longText('statusRemarks')->nullable();
            $table->integer('statusUpdatedBy')->nullable();
            $table->string('addedBy')->nullable();
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
        Schema::dropIfExists('bookings');
    }
}
