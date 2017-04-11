<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tDivision', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('groupId')->unsigned();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('isJV')->default(false);

            $table->foreign('groupId')->references('id')->on('tGroup')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tDivision');
    }
}
