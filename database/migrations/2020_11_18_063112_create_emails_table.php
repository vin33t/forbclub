<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->bigInteger('client_id')->nullable();
          $table->longText('account')->comment('Email Account Name i.e. MRD, Accounts etc.');
          $table->string('type')->nullable();
          $table->longText('uid')->comment('Current UID');
          $table->longText('sender');
          $table->longText('subject');
          $table->string('from');
          $table->string('to');
          $table->longText('cc');
          $table->longText('bcc');
          $table->longText('flags');
          $table->longText('text_body')->nullable();
          $table->longText('html_body')->nullable();
          $table->date('date');
          $table->time('time');
          $table->boolean('read')->default(0);
          $table->boolean('answered')->default(0);
          $table->boolean('ticket')->default(0);
          $table->boolean('flagged')->default(0);
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
        Schema::dropIfExists('emails');
    }
}
