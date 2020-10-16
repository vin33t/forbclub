<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('photo')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('email');
            $table->string('department');
//            $table->string('menuColors')->default('');
//            $table->string('themeLayout')->default(' ');
//            $table->string('navbarColors');
//            $table->string('navbarType');
//            $table->string('footerType');
//            $table->string('collapseSidebar');
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
        Schema::dropIfExists('employees');
    }
}
