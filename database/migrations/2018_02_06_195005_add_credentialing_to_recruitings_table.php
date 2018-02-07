<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCredentialingToRecruitingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipelineRecruiting', function (Blueprint $table) {
            $table->boolean('isCredentialing')->default(0)->nullable();
            $table->string('stopLight')->default('green')->nullable();
            $table->date('fileToCredentialing')->nullable();
            $table->string('enrollmentNotes')->nullable();
            $table->string('credentialingNotes')->nullable();
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
        Schema::table('tAccountPipelineRecruiting', function (Blueprint $table) {
            //
        });
    }
}
