<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxisMisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axis_mis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id')->nullable();
            $table->integer('meta_id')->nullable();
            $table->string('UMRNNO')->nullable();
            $table->string('SYSTEM_STATUS')->nullable();
            $table->string('REASONNAME')->nullable();
            $table->string('DEBTOR_CUSTOMER_REFERENCE_NO')->nullable();
            $table->string('PAYMENTTYPE')->nullable();
            $table->string('DEBTORACCOUNTNO')->nullable();
            $table->string('DEBITORBANKNAME')->nullable();
            $table->string('DEBTORBANKCODE')->nullable();
            $table->string('DEBTORNAME')->nullable();
            $table->string('CREDITORNAME')->nullable();
            $table->string('FREQUENCY')->nullable();
            $table->integer('AMOUNT')->nullable();
            $table->date('STARTDATE')->nullable();
            $table->date('ENDDATE')->nullable();
            $table->date('MANDATE_INITIATED_BUSINESS_DATE')->nullable();
            $table->date('SPONSOR_CHECKER_APPROVAL_DATE')->nullable();
            $table->date('MANDATE_CREATION_DATE')->nullable();
            $table->date('MANDATE_ACCEPTANCE_DATE')->nullable();
            $table->string('CREDITORUTILITYCODE')->nullable();
            $table->date('PRO_DATE')->nullable();
            $table->integer('LOT')->nullable();
            $table->integer('SRNO')->nullable();
            $table->integer('CLIENT_COD')->nullable();
            $table->string('OLD_UMRN')->nullable();
            $table->date('DATE')->nullable();
            $table->string('SP_BKCODE')->nullable();
            $table->string('ACTION')->nullable();
            $table->string('AC_TYPE')->nullable();
            $table->bigInteger('MOBILE')->nullable();
            $table->string('PICKUP_LOC')->nullable();
            $table->date('INWARD_DATE')->nullable();
            $table->string('SP_BANK')->nullable();
            $table->string('SCHEME')->nullable();
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
        Schema::dropIfExists('axis_mis');
    }
}
