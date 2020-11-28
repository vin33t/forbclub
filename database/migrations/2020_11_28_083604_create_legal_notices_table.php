<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_notices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('noticeReason');
            $table->date('noticeDate');
            $table->date('hearingDate');
            $table->longText('noticeDescription');
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
        Schema::dropIfExists('legal_notices');
    }
}
