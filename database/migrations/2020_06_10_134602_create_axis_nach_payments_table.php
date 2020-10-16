<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxisNachPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axis_nach_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('meta_id')->nullable();
            $table->longText('corporate_user_no')->nullable();
            $table->string('corporate_name')->nullable();
            $table->string('umrn')->nullable();
            $table->string('customer_to_be_debited')->nullable();
            $table->string('customer_ifsc')->nullable();
            $table->string('customer_debit_ac')->nullable();
            $table->string('transaction_id_ref')->nullable();
            $table->string('amount')->nullable();
            $table->string('date_of_transaction')->nullable();
            $table->string('status_description')->nullable();
            $table->string('reason_description')->nullable();
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
        Schema::dropIfExists('axis_nach_payments');
    }
}
