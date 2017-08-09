<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tContractLogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->integer('recruiterId')->unsigned();
            $table->integer('managerId')->unsigned();
            $table->integer('statusId')->unsigned();
            $table->integer('practiceId')->unsigned();
            $table->string('provider');
            $table->string('providerFirstName');
            $table->string('providerLastName');
            $table->integer('specialtyId')->unsigned();
            $table->integer('divisionId')->unsigned();
            $table->date('contractOutDate');
            $table->date('contractInDate')->nullable();
            $table->date('sentToQADate')->unsigned();
            $table->date('counterSigDate')->unsigned();
            $table->date('sentToPayrollDate')->unsigned();
            $table->date('projectedStartDate')->unsigned();
            $table->date('actualStartDate')->unsigned();
            $table->integer('numOfHours')->unsigned();
            $table->integer('contractTypeId')->unsigned();
            $table->integer('numOfRevisions')->unsigned();
            $table->integer('contractNoteId')->unsigned();
            $table->text('comments')->nullable();
            $table->integer('numOfAmendmentsIn')->unsigned();
            $table->integer('contractCoordinatorId')->unsigned();
            $table->integer('positionId')->unsigned();
            $table->double('value');

            // $table->foreign('accountId')->references('id')->on('tAccount')->onDelete('cascade');
            // $table->foreign('recruiterId')->references('id')->on('tEmployee')->onDelete('cascade');
            // $table->foreign('managerId')->references('id')->on('tEmployee')->onDelete('cascade');
            // $table->foreign('statusId')->references('id')->on('tContractStatus')->onDelete('cascade');
            // $table->foreign('practiceId')->references('id')->on('tPractice')->onDelete('cascade');
            // $table->foreign('specialtyId')->references('id')->on('tSpecialty')->onDelete('cascade');
            // $table->foreign('divisionId')->references('id')->on('tDivision')->onDelete('cascade');
            // $table->foreign('contractTypeId')->references('id')->on('tContractType')->onDelete('cascade');
            // $table->foreign('contractNoteId')->references('id')->on('tContractType')->onDelete('cascade');
            // $table->foreign('contractCoordinatorId')->references('id')->on('tEmploye')->onDelete('cascade');
            // $table->foreign('positionId')->references('id')->on('tPosition')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tContractLogs');
    }
}
