<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToRosterBenchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipelineRosterBench', function (Blueprint $table) {
            $table->date('privilegeGoal')->nullable();
            $table->date('appToHospital')->nullable();
            $table->integer('stage')->nullable();
            $table->string('enrollmentStatus')->nullable();
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
