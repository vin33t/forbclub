<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_payments', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('client_id')->nullable();
            $table->date('paymentDate');
            $table->integer('amount');
            $table->string('cardType', 191);
            $table->string('bankName', 191);
            $table->string('cardLastFourDigits', 191);
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
        Schema::dropIfExists('card_payments');
    }
}
