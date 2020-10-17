<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('documents', function (Blueprint $table) {
        $table->bigInteger('id', true)->unsigned();
        $table->integer('client_id');
        $table->string('type', 191);
        $table->string('number', 191);
        $table->string('url', 191)->nullable();
        $table->integer('added_by')->nullable()->comment('Employee user Id');
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
        Schema::dropIfExists('documents');
    }
}
