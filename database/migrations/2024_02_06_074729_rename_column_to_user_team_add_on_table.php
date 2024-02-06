<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToUserTeamAddOnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_team_app', function (Blueprint $table) {
            $table->renameColumn('add_on_uuid', 'app_uuid');
        });

        Schema::table('team_app', function (Blueprint $table) {
            $table->renameColumn('add_on_uuid', 'app_uuid');
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
            $table->renameColumn('app_uuid', 'add_on_uuid');
        });

        Schema::table('team_app', function (Blueprint $table) {
            $table->renameColumn('app_uuid', 'add_on_uuid');
        });
    }
}
