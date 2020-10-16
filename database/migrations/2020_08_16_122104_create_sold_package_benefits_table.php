<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldPackageBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sold_package_benefits', function (Blueprint $table) {
            $table->id();
            $table->integer('clientId');
            $table->integer('soldPackageId');
            $table->string('benefitName');
            $table->longText('benefitDescription');
            $table->longText('benefitConditions');
            $table->date('benefitValidity');
            $table->string('benefitStatus')->comment('availed/booking/ongoing')->nullable();
            $table->date('benefitBookedOn')->comment('Booking Request Initial Date')->nullable();
            $table->date('benefitAvailedOn')->comment('Booking Availed Date')->nullable();
            $table->integer('benefitAuthorizedBy')->comment('Booking Authorized By')->nullable();
            $table->date('benefitAuthorizedDate')->comment('Booking Authorized On')->nullable();
            $table->longText('benefitAuthorized Remarks')->comment('Booking Holiday Authorized Remarks')->nullable();
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
        Schema::dropIfExists('sold_package_benefits');
    }
}
