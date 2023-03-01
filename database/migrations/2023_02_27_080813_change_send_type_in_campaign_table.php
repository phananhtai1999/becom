<?php

use Illuminate\Database\Migrations\Migration;

class ChangeSendTypeInCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       \Illuminate\Support\Facades\DB::statement("ALTER TABLE `campaigns` CHANGE `send_type` `send_type` ENUM('sms','email','telegram', 'viber') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `campaigns` CHANGE `send_type` `send_type` ENUM('sms','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}
