<?php

use Database\Seeders\MoveValueWebsiteUuidToWebsiteEmailSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveWebsiteUuidDataInEmailToWebsiteEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new MoveValueWebsiteUuidToWebsiteEmailSeeder())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
