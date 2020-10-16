<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('clients', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('phone')->nullable();
      $table->string('altPhone')->nullable();
      $table->string('email')->nullable();
      $table->string('altEmail')->nullable();
      $table->date('birthDate')->nullable();
      $table->string('address')->nullable();
      $table->string('photo')->nullable();
      $table->longText('slug');
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
    Schema::dropIfExists('clients');
  }
}
