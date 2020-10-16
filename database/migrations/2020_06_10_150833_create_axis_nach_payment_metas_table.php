<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxisNachPaymentMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axis_nach_payment_metas', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('transactions');
            $table->bigInteger('amount');
            $table->bigInteger('success')->default(0);
            $table->bigInteger('success_amount')->default(0);
            $table->bigInteger('failure')->default(0);
            $table->bigInteger('failure_amount')->default(0);
            $table->date('upload_date');
            $table->bigInteger('notified')->default(0);
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
        Schema::dropIfExists('axis_nach_payment_metas');
    }
}
