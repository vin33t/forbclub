<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_requests', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('client_id');
          $table->integer('added_by');
          $table->integer('amount');
          $table->longText('reason')->nullable();
          $table->string('through');
          $table->Date('refund_date');

          $table->integer('accepted_denied')->nullable();
          $table->integer('accepted_denied_by')->nullable();
          $table->longText('accepted_denied_remarks')->nullable();
          $table->Datetime('accepted_denied_datetime')->nullable();
          $table->string('accepted_denied_client_status_changed')->nullable();

          $table->integer('approved_rejected')->nullable();
          $table->integer('approved_rejected_by')->nullable();
          $table->integer('approved_rejected_amount')->nullable();
          $table->longText('approved_rejected_remarks')->nullable();
          $table->Datetime('approved_rejected_datetime')->nullable();
          $table->string('approved_rejected_client_status_changed')->nullable();

          $table->Datetime('approval_accounts_datetime')->nullable();
          $table->integer('approval_accounts_by')->nullable();
          $table->integer('approval_accounts_amount')->nullable();
          $table->longText('approval_accounts_remarks')->nullable();
          $table->string('approval_accounts_client_status_changed')->nullable();

          $table->Date('date_of_payment')->nullable();
          $table->string('mode_of_payment')->nullable();
          $table->string('last_four_digits')->nullable();
          $table->string('card_name')->nullable();
          $table->string('bank_name')->nullable();
          $table->string('cheque_number')->nullable();

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
        Schema::dropIfExists('refund_requests');
    }
}
