<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenueExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venue_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('venue_id');
            $table->string('expense_name');
            $table->integer('expense_amount');
            $table->longText('expense_details');
            $table->boolean('paid')->default(0);
            $table->date('paid_on')->nullable();
            $table->longText('payment_remarks')->nullable();
            $table->longText('expenseBill')->nullable();
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
        Schema::dropIfExists('venue_expenses');
    }
}
