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
            // $table->float('fullTimeHoursPhys')->nullable()->change();
            // $table->float('fullTimeHoursApps')->nullable()->change();
            // $table->float('staffPhysicianFTEHaves')->nullable()->change();
            // $table->float('staffPhysicianFTENeeds')->nullable()->change();
            // $table->float('staffPhysicianFTEOpenings')->nullable()->change();
            // $table->float('staffAppsFTEHaves')->nullable()->change();
            // $table->float('staffAppsFTENeeds')->nullable()->change();
            // $table->float('staffAppsFTEOpenings')->nullable()->change();
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
