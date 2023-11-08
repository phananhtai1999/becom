<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamAddOnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_add_on', function (Blueprint $table) {
            $table->unsignedBigInteger('team_uuid');
            $table->unsignedBigInteger('add_on_uuid');
            $table->primary(['team_uuid', 'add_on_uuid']);
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
        Schema::dropIfExists('team_add_on');
    }
}
