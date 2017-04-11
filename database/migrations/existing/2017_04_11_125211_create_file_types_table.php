<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tFilelogFileType', function (Blueprint $table) {
            $table->increments('fileTypeId');
            $table->string('fileTypeName');
            $table->string('fileTypeSearchString');
            $table->integer('feedId')->unsigned();
            $table->boolean('enabledFlag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tFilelogFileType');
    }
}
