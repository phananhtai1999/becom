<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ConfigSeeder;

class CreateSmtpAutoConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new ConfigSeeder())->run();
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
