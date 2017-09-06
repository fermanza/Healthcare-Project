<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSMDAMDFieldsToTAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccount', function (Blueprint $table) {
            $table->boolean('hasSMD')->default(0);
            $table->boolean('hasAMD')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tAccount', function (Blueprint $table) {
            //
        });
    }
}
