<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountToEmployee', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->integer('employeeId')->unsigned();
            $table->integer('positionTypeId')->unsigned();
            $table->boolean('isPrimary')->default(false);

            $table->foreign('accountId')->references('id')->on('tAccount')->onDelete('cascade');
            $table->foreign('employeeId')->references('id')->on('tEmployee')->onDelete('cascade');
            $table->foreign('positionTypeId')->references('id')->on('tPositionType')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tAccountToEmployee');
    }
}
