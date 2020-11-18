<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sents', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->bigInteger('emails_id')->nullable();
          $table->bigInteger('client_id')->nullable();
          $table->longText('to');
          $table->longText('from');
          $table->longText('cc')->nullable();
          $table->longText('bcc')->nullable();
          $table->longText('subject')->nullable();
          $table->longText('mail');
          $table->bigInteger('sent_by');
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
        Schema::dropIfExists('email_sents');
    }
}
