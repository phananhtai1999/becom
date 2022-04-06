<?php

use Database\Seeders\RoleSeeder;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultRolesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new RoleSeeder())->run();
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
