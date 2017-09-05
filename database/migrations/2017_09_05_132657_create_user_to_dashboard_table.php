<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserToDashboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tUserToDashboard', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned()->nullable();
            $table->integer('dashboardId')->unsigned()->nullable();

            $table->foreign('userId')->references('id')->on('tUser')->onDelete('cascade');
            $table->foreign('dashboardId')->references('id')->on('tDashboard')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tUserToDashboard');
    }
}
