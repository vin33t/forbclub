<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_months', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id')->unsigned();
            $table->integer('paidMonth')->unsigned();
            $table->integer('paidYear')->unsigned();
            $table->string('transactionType');
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
        Schema::dropIfExists('transaction_months');
    }
}
