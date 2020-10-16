<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYesNachPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yes_nach_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('meta_id')->nullable();
            $table->string('ITEM_TYPE')->nullable();
            $table->string('ITEM_REFERENCE')->nullable();
            $table->string('ITEM_SEQUENCE_NUMBER')->nullable();
            $table->string('STATUS')->nullable();
            $table->string('CLEARING_STATUS')->nullable();
            $table->date('VALUE_DATE')->nullable();
            $table->string('SENDER')->nullable();
            $table->string('RECEIVER')->nullable();
            $table->string('REASON_CODE')->nullable();
            $table->string('CURRENCY')->nullable();
            $table->string('AMOUNT')->nullable();
            $table->string('RECEIVER_ACCOUNT')->nullable();
            $table->string('NAME')->nullable();
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
        Schema::dropIfExists('yes_nach_payments');
    }
}
