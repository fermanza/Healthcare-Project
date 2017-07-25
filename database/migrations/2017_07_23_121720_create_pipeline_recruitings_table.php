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
            $table->date('interview')->nullable();
            $table->date('contractOut')->nullable();
            $table->date('contractIn')->nullable();
            $table->date('firstShift')->nullable();
            $table->string('notes', 2047)->nullable();
            $table->date('declined')->nullable();
            $table->string('declinedReason', 2047)->nullable();
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
