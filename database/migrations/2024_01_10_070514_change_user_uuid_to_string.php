<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserUuidToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('leader_uuid')->change();
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->string('manager_uuid')->change();
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->string('manager_uuid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->bigInteger('leader_uuid')->change();
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->bigInteger('manager_uuid')->change();
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->bigInteger('manager_uuid')->change();
        });
    }
}
