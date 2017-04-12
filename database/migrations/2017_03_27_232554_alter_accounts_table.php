<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccount', function (Blueprint $table) {
            $table->string('photoPath')->nullable();
            $table->string('googleAddress')->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('zipCode')->nullable();
            $table->string('country')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('physiciansNeeded')->unsigned()->default(0);
            $table->integer('appsNeeded')->unsigned()->default(0);
            $table->integer('physicianHoursPerMonth')->unsigned()->default(0);
            $table->integer('appHoursPerMonth')->unsigned()->default(0);
            $table->boolean('pressRelease')->default(false);
            $table->date('pressReleaseDate')->nullable();
            $table->boolean('managementChangeMailers')->default(false);
            $table->boolean('recruitingMailers')->default(false);
            $table->boolean('emailBlast')->default(false);
            $table->boolean('purlCampaign')->default(false);
            $table->boolean('marketingSlick')->default(false);
            $table->boolean('collaborationRecruitingTeam')->default(false);
            $table->string('collaborationRecruitingTeamNames')->nullable();
            $table->boolean('compensationGrid')->default(false);
            $table->string('compensationGridBonuses')->nullable();
            $table->boolean('recruitingIncentives')->default(false);
            $table->string('recruitingIncentivesDescription')->nullable();
            $table->boolean('locumCompaniesNotified')->default(false);
            $table->boolean('searchFirmsNotified')->default(false);
            $table->boolean('departmentsCoordinated')->default(false);
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
