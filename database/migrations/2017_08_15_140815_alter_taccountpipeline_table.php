<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTaccountpipelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipeline', function (Blueprint $table) {
            // $table->float('fullTimeHoursPhys')->nullable();
            // $table->float('fullTimeHoursApps')->nullable();
            // $table->float('staffPhysicianFTEHaves')->nullable();
            // $table->float('staffPhysicianFTENeeds')->nullable();
            // $table->float('staffPhysicianFTEOpenings')->nullable();
            // $table->float('staffAppsFTEHaves')->nullable();
            // $table->float('staffAppsFTENeeds')->nullable();
            // $table->float('staffAppsFTEOpenings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tAccountPipeline', function (Blueprint $table) {
            //
        });
    }
}
