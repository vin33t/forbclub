<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->date('expenseDate');
            $table->longText('expenseType');
            $table->bigInteger('amount');
            $table->longText('expenseBill');
            $table->longText('remarks');
            $table->boolean('reimbursed')->default(0);
            $table->date('reimbursedOn')->nullable();
            $table->longText('reimbursedRemarks')->nullable();
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
        Schema::dropIfExists('reimbursements');
    }
}
