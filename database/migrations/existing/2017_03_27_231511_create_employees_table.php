<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tEmployee', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('personId')->unsigned();
            $table->integer('employementStatusId')->unsigned();
            $table->integer('positionTypeId')->unsigned();
            $table->integer('managerId')->unsigned();
            $table->string('employeeType');
            $table->double('EDPercent')->nullable();
            $table->double('IPSPercent')->nullable();

            $table->foreign('personId')->references('id')->on('tPerson')->onDelete('cascade');
            $table->foreign('employementStatusId')->references('id')->on('tEmployementStatus')->onDelete('cascade');
            $table->foreign('positionTypeId')->references('id')->on('tPositionType')->onDelete('cascade');
            $table->foreign('managerId')->references('id')->on('tEmployee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tEmployee');
    }
}
