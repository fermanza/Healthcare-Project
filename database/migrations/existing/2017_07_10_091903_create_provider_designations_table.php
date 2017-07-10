<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tProviderDesignation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::table('tContractLogs', function (Blueprint $table) {
            $table->integer('providerDesignationId')->unsigned()->nullable();

            $table->foreign('providerDesignationId')->references('id')->on('tProviderDesignation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tProviderDesignation');
    }
}
