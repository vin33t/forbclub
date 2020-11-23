<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsfPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asf_payments', function (Blueprint $table) {
            $table->id();
          $table->integer('client_id')->nullable();
          $table->date('paymentDate')->nullable();
          $table->integer('amount');
          $table->year('year');
          $table->longText('remarks')->nullable();
          $table->boolean('waved_off')->default(0);
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
        Schema::dropIfExists('asf_payments');
    }
}
