<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserToOperatingUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tUserToOperatingUnit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned()->nullable();
            $table->integer('operatingUnitId')->unsigned()->nullable();

            $table->foreign('userId')->references('id')->on('tUser')->onDelete('cascade');
            $table->foreign('operatingUnitId')->references('id')->on('tOperatingUnit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tUserToOperatingUnit');
    }
}
