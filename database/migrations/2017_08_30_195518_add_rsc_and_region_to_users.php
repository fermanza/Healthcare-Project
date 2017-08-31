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

            $table->integer('operatingUnitId')->unsigned()->nullable();
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
