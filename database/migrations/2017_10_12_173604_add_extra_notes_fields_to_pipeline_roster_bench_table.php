<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraNotesFieldsToPipelineRosterBenchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipelineRosterBench', function (Blueprint $table) {
            $table->string('enrollmentNotes')->nullable();
            $table->string('credentialingNotes')->nullable();
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
