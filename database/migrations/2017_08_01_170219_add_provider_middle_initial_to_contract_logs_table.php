<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderMiddleInitialToContractLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tContractLogs', function (Blueprint $table) {
            $table->string('providerMiddleInitial')->nullable();
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
            $table->dropColumn(['providerMiddleInitial']);
        });
    }
}
