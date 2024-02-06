<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypeOfAppUuidToUserTeamAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_team_app', function (Blueprint $table) {
            $table->string('app_uuid')->change();
        });

        Schema::table('team_app', function (Blueprint $table) {
            $table->string('app_uuid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_team_app', function (Blueprint $table) {
            $table->integer('app_uuid')->change();
        });

        Schema::table('team_app', function (Blueprint $table) {
            $table->integer('app_uuid')->change();
        });
    }
}
