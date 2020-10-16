<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYesMisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yes_mis', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('MANDATE_DATE')->nullable();
            $table->string('MANDATE_ID')->nullable();
            $table->string('UMRN')->nullable();
            $table->string('CUST_REF_NO')->nullable();
            $table->string('SCH_REF_NO')->nullable();
            $table->string('CUST_NAME')->nullable();
            $table->string('BANK')->nullable();
            $table->string('BRANCH')->nullable();
            $table->string('BANK_CODE')->nullable();
            $table->string('AC_TYPE')->nullable();
            $table->string('AC_NO')->nullable();
            $table->string('AMOUNT')->nullable();
            $table->string('FREQUENCY')->nullable();
            $table->string('DEBIT_TYPE')->nullable();
            $table->string('START_DATE')->nullable();
            $table->string('END_DATE')->nullable();
            $table->string('UNTIL_CANCEL')->nullable();
            $table->string('TEL_NO')->nullable();
            $table->string('MOBILE_NO')->nullable();
            $table->string('MAIL_ID')->nullable();
            $table->string('UPLOAD_DATE')->nullable();
            $table->string('RESPONSE_DATE')->nullable();
            $table->string('UTILITY_CODE')->nullable();
            $table->string('UTILITY_NAME')->nullable();
            $table->string('STATUS')->nullable();
            $table->string('STATUS_CODE')->nullable();
            $table->string('REASON')->nullable();
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
        Schema::dropIfExists('yes_mis');
    }
}
