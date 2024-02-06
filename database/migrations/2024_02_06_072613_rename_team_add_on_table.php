<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTeamAddOnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('team_add_on', 'team_app');
        Schema::rename('user_team_add_on', 'user_team_app');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('team_app', 'team_add_on');
        Schema::rename('user_team_app', 'user_team_add_on');
    }
}
