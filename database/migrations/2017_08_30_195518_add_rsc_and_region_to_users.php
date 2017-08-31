<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRscAndRegionToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tUser', function (Blueprint $table) {
            $table->integer('RSCId')->unsigned()->nullable();
            $table->foreign('RSCId')->references('id')->on('tRSC')->onDelete('cascade');

            $table->integer('operatingUnitId')->unsigned()->nullable();
            $table->foreign('operatingUnitId')->references('id')->on('tOperatingUnit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tUser', function (Blueprint $table) {
            //
        });
    }
}
