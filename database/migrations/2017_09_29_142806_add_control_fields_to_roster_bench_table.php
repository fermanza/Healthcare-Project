<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControlFieldsToRosterBenchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipelineRosterBench', function (Blueprint $table) {
            $table->dateTime('lastUpdated')->nullable();
            $table->integer('lastUpdatedBy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tAccountPipelineRosterBench', function (Blueprint $table) {
            //
        });
    }
}
