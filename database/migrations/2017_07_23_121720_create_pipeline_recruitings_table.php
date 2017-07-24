<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineRecruitingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAccountPipelineRecruiting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pipelineId')->unsigned();
            $table->enum('type', config('pipeline.recruiting_types'));
            $table->enum('contract', config('pipeline.contract_types'));
            $table->string('name');
            $table->date('interview');
            $table->date('contractOut');
            $table->date('contractIn');
            $table->date('firstShift');
            $table->string('notes', 1000)->nullable();
            $table->dateTime('declined')->nullable();
            $table->dateTime('resigned')->nullable();
            $table->string('reason', 1000)->nullable();
            $table->date('application')->nullable();

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
        Schema::dropIfExists('tAccountPipelineRecruiting');
    }
}
