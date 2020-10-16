<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_payments', function (Blueprint $table) {
            $table->id();
          $table->integer('client_id')->nullable();
          $table->date('paymentDate');
          $table->integer('amount');
          $table->string('receiptNumber');
          $table->longText('remarks');
          $table->boolean('isDp')->default(0);
          $table->boolean('isAddon')->default(0);
          $table->boolean('isRealized')->default(0);
          $table->date('realizationOn')->nullable();
          $table->bigInteger('realizationBy')->nullable();
          $table->json('realizationDetails')->nullable();
          $table->longText('realizationRemarks')->nullable();
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
        Schema::dropIfExists('cash_payments');
    }
}
