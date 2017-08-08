<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDirectorIdToRscTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tRSC', function (Blueprint $table) {
            $table->integer('directorId')->unsigned()->nullable();

            $table->foreign('directorId')->references('id')->on('tEmployee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tRSC', function (Blueprint $table) {
            //
        });
    }
}
