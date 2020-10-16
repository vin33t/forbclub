<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePDCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_d_c_s', function (Blueprint $table) {
            $table->id();
          $table->integer('client_id');
          $table->integer('transaction_id')->nullable();
          $table->integer('cheque_no');
          $table->integer('amount');
          $table->string('micr_number')->nullable();
          $table->string('branch_name')->nullable();
          $table->string('branch_address')->nullable();
          $table->date('date_of_execution');
          $table->longText('remarks')->nullable();
          $table->integer('employee_id')->default(1);
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
        Schema::dropIfExists('p_d_c_s');
    }
}
