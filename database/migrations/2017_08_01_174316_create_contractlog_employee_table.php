<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractlogEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tContractLogToEmployee', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractLogId')->unsigned();
            $table->integer('employeeId')->unsigned();

            $table->foreign('contractLogId')->references('id')->on('tContractLogs')->onDelete('cascade');
            $table->foreign('employeeId')->references('id')->on('tEmployee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tContractLogToEmployee');
    }
}
