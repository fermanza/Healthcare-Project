<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControlFieldsToLocumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccountPipelineLocum', function (Blueprint $table) {
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
        Schema::table('tAccountPipelineLocum', function (Blueprint $table) {
            //
        });
    }
}
