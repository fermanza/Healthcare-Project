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
            $table->string('employeeType');
            $table->boolean('isFullTime')->default(false);

            $table->foreign('personId')->references('id')->on('tPerson')->onDelete('cascade');
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
