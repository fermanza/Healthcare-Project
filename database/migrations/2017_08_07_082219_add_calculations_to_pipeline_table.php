<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalculationsToPipelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipeline', function (Blueprint $table) {
            $table->integer('fullTimeHoursPhys')->default(0);
            $table->integer('fullTimeHoursApps')->default(0);
            $table->double('staffPhysicianFTEHaves')->default(0)->nullable();
            $table->double('staffPhysicianFTENeeds')->default(0)->nullable();
            $table->double('staffPhysicianFTEOpenings')->default(0)->nullable();
            $table->double('staffAppsFTEHaves')->default(0)->nullable();
            $table->double('staffAppsFTENeeds')->default(0)->nullable();
            $table->double('staffAppsFTEOpenings')->default(0)->nullable();
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
            $table->dropColumn([
                'fullTimeHoursPhys',
                'fullTimeHoursApps',
                'staffPhysicianFTEHaves',
                'staffPhysicianFTENeeds',
                'staffPhysicianFTEOpenings',
                'staffAppsFTEHaves',
                'staffAppsFTENeeds',
                'staffAppsFTEOpenings',
            ]);
        });
    }
}
