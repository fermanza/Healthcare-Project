<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tSiteCodeHistory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accountId')->unsigned();
            $table->string('siteCode');
            $table->timestamps();

            $table->foreign('accountId')->references('id')->on('tAccount')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tSiteCodeHistory');
    }
}
