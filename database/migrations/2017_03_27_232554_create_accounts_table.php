<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recruiter_id')->unsigned();
            $table->integer('manager_id')->unsigned();
            $table->integer('practice_id')->unsigned();
            $table->integer('division_id')->unsigned();
            $table->string('name');
            $table->string('site_code');
            $table->string('photo_path')->nullable();
            $table->string('google_address')->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->dateTime('start_date');
            $table->integer('physicians_needed')->unsigned();
            $table->integer('apps_needed')->unsigned();
            $table->integer('physician_hours_per_month')->unsigned();
            $table->integer('app_hours_per_month')->unsigned();

            $table->boolean('press_release')->default(false);
            $table->date('press_release_date')->nullable();
            $table->boolean('management_change_mailers')->default(false);
            $table->boolean('recruiting_mailers')->default(false);
            $table->boolean('email_blast')->default(false);
            $table->boolean('purl_campaign')->default(false);
            $table->boolean('marketing_slick')->default(false);
            $table->boolean('collaboration_recruiting_team')->default(false);
            $table->string('collaboration_recruiting_team_names')->nullable();
            $table->boolean('compensation_grid')->default(false);
            $table->string('compensation_grid_bonuses')->nullable();
            $table->boolean('recruiting_incentives')->default(false);
            $table->string('recruiting_incentives_description')->nullable();
            $table->boolean('locum_companies_notified')->default(false);
            $table->boolean('search_firms_notified')->default(false);
            $table->boolean('departments_coordinated')->default(false);
            $table->timestamps();

            $table->foreign('recruiter_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
