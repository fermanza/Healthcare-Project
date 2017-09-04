<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tAccount', function (Blueprint $table) {
            $table->string('requirements')->nullable();
            $table->string('fees')->nullable();
            $table->string('applications')->nullable();
            $table->string('meetings')->nullable();
            $table->string('other')->nullable();
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
