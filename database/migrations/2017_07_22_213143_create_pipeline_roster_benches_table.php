<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineRosterBenchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountPipelineRosterBench', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pipelineId')->unsigned();
            $table->enum('place', config('pipeline.places'));
            $table->enum('activity', config('pipeline.activities'));
            $table->string('name');
            $table->integer('hours')->unsigned();
            $table->date('interview');
            $table->date('contractOut');
            $table->date('contractIn');
            $table->date('firstShift');

            $table->foreign('pipelineId')->references('id')->on('tAccountPipeline');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tAccountPipelineRosterBench');
    }
}
