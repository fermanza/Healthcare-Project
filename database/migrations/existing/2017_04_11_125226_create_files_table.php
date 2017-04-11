<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tFilelog', function (Blueprint $table) {
            $table->increments('fileLogId');
            $table->integer('fileTypeId')->unsigned()->nullable();
            $table->integer('statusTypeId')->unsigned()->nullable();
            $table->integer('feedId')->unsigned();
            $table->string('fileName');
            $table->string('path');
            $table->dateTime('downloadDate')->nullable();
            $table->dateTime('processedDate')->nullable();
            $table->dateTime('modifiedDate')->nullable();

            $table->foreign('fileTypeId')->references('fileTypeId')->on('tFilelogFileType')->onDelete('cascade');
            $table->foreign('statusTypeId')->references('statusTypeId')->on('tFilelogStatusType')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tFilelog');
    }
}
