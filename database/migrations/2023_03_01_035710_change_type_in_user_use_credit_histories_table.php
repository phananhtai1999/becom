<?php

use Illuminate\Database\Migrations\Migration;

class ChangeTypeInUserUseCreditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `user_use_credit_histories` CHANGE `type` `type` ENUM('sms','email','telegram', 'viber') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `user_use_credit_histories` CHANGE `type` `type` ENUM('sms','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email'");
    }
}
