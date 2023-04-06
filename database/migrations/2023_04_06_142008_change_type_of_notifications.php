<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class ChangeTypeOfNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `notifications`
        CHANGE `type` `type` enum('campaign', 'account', 'scenario', 'support', 'payment') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'campaign' AFTER `uuid`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `notifications`
        CHANGE `type` `type` enum('campaign', 'login', 'scenario') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'campaign' AFTER `uuid`;");
    }
}
