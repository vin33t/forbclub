<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldPackagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sold_packages', function (Blueprint $table) {
      $table->id();
      $table->integer('clientId');
      $table->string('mafNo')->comment('ForbClub Leisureship ID');
      $table->string('fclpId');
      $table->string('branch')->nullable();
      $table->string('saleBy')->nullable();
      $table->string('enrollmentDate')->nullable();
      $table->string('productType')->nullable();
      $table->string('productTenure')->nullable();
      $table->string('productName')->nullable();
      $table->string('productCost')->nullable();
      $table->string('modeOfPayment')->nullable();
      $table->string('noOfEmi')->nullable();
      $table->string('emiAmount')->nullable();
      $table->string('asc')->nullable();
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
    Schema::dropIfExists('sold_packages');
  }
}
