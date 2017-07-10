<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountContractlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tContractLogToAccounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractLogId')->unsigned();
            $table->integer('accountId')->unsigned();

            $table->foreign('contractLogId')->references('id')->on('tContractLogs')->onDelete('cascade');
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
        Schema::dropIfExists('tContractLogToAccounts');
    }
}
