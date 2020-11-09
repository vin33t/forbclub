<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientHolidayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_holiday_transactions', function (Blueprint $table) {
            $table->id();
          $table->integer('client_holiday_details_id');
          $table->integer('client_id');

          $table->string('amount');
          $table->date('date_of_payment');
          $table->boolean('paid');

          $table->string('mode_of_payment')->nullable();

          $table->string('last_four_card_digits')->nullable();
          $table->string('card_description')->nullable();

          $table->string('bank_name')->nullable();

          $table->string('cheque_number')->nullable();
          $table->boolean('cancelled')->default(0);
          $table->date('cancelled_on')->nullable();
          $table->integer('cancelled_by')->nullable();
          $table->boolean('add_on');
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
        Schema::dropIfExists('client_holiday_transactions');
    }
}
