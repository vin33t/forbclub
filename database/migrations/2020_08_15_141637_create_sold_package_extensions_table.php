<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldPackageExtensionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sold_package_extensions', function (Blueprint $table) {
      $table->id();
      $table->integer('clientId');
      $table->integer('soldPackageId');
      $table->date('extensionTill')->nullable();
      $table->longText('extensionReason')->nullable();
      $table->boolean('extensionAuthorized')->default(false);
      $table->integer('extensionAuthorizedBy')->nullable();
      $table->dateTime('extensionAuthorizedOn')->nullable();
      $table->longText('extensionAuthorizationRemarks')->nullable();
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
    Schema::dropIfExists('sold_package_extensions');
  }
}
