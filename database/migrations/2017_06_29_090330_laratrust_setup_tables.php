<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class LaratrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('tRole', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
        });

        // Create table for storing permissions
        Schema::create('tPermission', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
        });

        // Create table for associating roles to users and teams (Many To Many Polymorphic)
        Schema::create('tRoleToUser', function (Blueprint $table) {
            $table->integer('userId')->unsigned();
            $table->integer('roleId')->unsigned();
            $table->string('user_type');

            $table->foreign('roleId')->references('id')->on('tRole')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['userId', 'roleId', 'user_type']);
        });

        // Create table for associating permissions to users (Many To Many Polymorphic)
        Schema::create('tPermissionToUser', function (Blueprint $table) {
            $table->integer('userId')->unsigned();
            $table->integer('permissionId')->unsigned();
            $table->string('user_type');

            $table->foreign('permissionId')->references('id')->on('tPermission')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['userId', 'permissionId', 'user_type']);
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('tPermissionToRole', function (Blueprint $table) {
            $table->integer('permissionId')->unsigned();
            $table->integer('roleId')->unsigned();

            $table->foreign('permissionId')->references('id')->on('tPermission')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('tRole')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permissionId', 'roleId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('tPermissionToUser');
        Schema::dropIfExists('tPermissionToRole');
        Schema::dropIfExists('tPermission');
        Schema::dropIfExists('tRoleToUser');
        Schema::dropIfExists('tRole');
    }
}
