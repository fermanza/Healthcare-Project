<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccount', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('divisionId')->unsigned()->nullable();
            $table->string('name');
            $table->string('siteCode');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->dateTime('startDate')->nullable();
            
            $table->foreign('divisionId')->references('id')->on('tDivision')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tAccount');
    }
}
