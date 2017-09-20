<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeclinedNeverReturnedToContractLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tContractLogs', function (Blueprint $table) {
            $table->boolean('declined')->default(0)->nullable();
            $table->boolean('neverReturned')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tContractLogs', function (Blueprint $table) {
            //
        });
    }
}
