<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountPracticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountToPractice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->integer('practiceId')->unsigned();

            $table->foreign('accountId')->references('id')->on('tAccount')->onDelete('cascade');
            $table->foreign('practiceId')->references('id')->on('tPractice')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tAccountToPractice');
    }
}
