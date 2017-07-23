<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountPipeline', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->string('medicalDirector')->nullable();
            $table->string('rmd')->nullable();
            $table->string('svp')->nullable();
            $table->string('dca')->nullable();
            $table->enum('practiceTime', config('pipeline.practice_times'))->default('hours');
            $table->integer('staffPhysicianHaves')->default(0);
            $table->integer('staffPhysicianNeeds')->default(0);
            $table->integer('staffPhysicianOpenings')->default(0);
            $table->integer('staffAppsHaves')->default(0);
            $table->integer('staffAppsNeeds')->default(0);
            $table->integer('staffAppsOpenings')->default(0);

            $table->foreign('accountId')->references('id')->on('tAccount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tAccountPipeline');
    }
}
