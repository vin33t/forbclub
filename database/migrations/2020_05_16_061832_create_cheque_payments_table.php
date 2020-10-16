<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_payments', function (Blueprint $table) {
          $table->bigInteger('id', true)->unsigned();
          $table->integer('client_id')->nullable();
          $table->date('paymentDate');
          $table->integer('amount');
          $table->string('chequeNumber', 191);
          $table->string('chequeIssuer', 191);
          $table->string('chequeClearingBank', 191);
          $table->longText('chequeStatus');
          $table->boolean('isDp')->default(0);
          $table->boolean('isAddon')->default(0);
          $table->boolean('isRealized')->default(0);
          $table->date('realizationOn')->nullable();
          $table->bigInteger('realizationBy')->nullable();
          $table->json('realizationDetails')->nullable();
          $table->longText('realizationRemarks')->nullable();
          $table->longText('remarks')->nullable();
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
        Schema::dropIfExists('cheque_payments');
    }
}
