<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineLocumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountPipelineLocum', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pipelineId')->unsigned();
            $table->enum('type', config('pipeline.recruiting_types'));
            $table->enum('contract', config('pipeline.contract_types'))->nullable();
            $table->string('name');
            $table->string('agency');
            $table->date('potentialStart');
            $table->text('credentialingNotes')->nullable();
            $table->integer('shiftsOffered')->unsigned();
            $table->date('startDate');
            $table->text('comments')->nullable();
            $table->dateTime('declined')->nullable();
            $table->dateTime('resigned')->nullable();
            $table->text('reason')->nullable();
            $table->date('application')->nullable();
            $table->date('interview')->nullable();
            $table->date('contractOut')->nullable();

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
        Schema::dropIfExists('tAccountPipelineLocum');
    }
}
