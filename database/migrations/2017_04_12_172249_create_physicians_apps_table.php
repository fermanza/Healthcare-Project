<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysiciansAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tPhysicianAppHistory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->integer('physiciansNeeded')->unsigned()->nullable();
            $table->integer('appsNeeded')->unsigned()->nullable();
            $table->integer('physicianHoursPerMonth')->unsigned()->nullable();
            $table->integer('appHoursPerMonth')->unsigned()->nullable();
            $table->date('physicianAppsChangeDate');
            $table->string('physicianAppsChangeReason');
            $table->timestamps();

            $table->foreign('accountId')->references('id')->on('tAccount')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tPhysicianAppHistory');
    }
}
