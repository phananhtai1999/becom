<?php

use App\Models\UserTeamAddOn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTeamAddonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_team_add_on', function (Blueprint $table) {
            $table->bigIncrements('uuid');
            $table->bigInteger('user_team_uuid');
            $table->bigInteger('add_on_uuid');
            $table->string('status')->default(UserTeamAddOn::ACTIVE_STATUS);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_team_add_on');
    }
}
