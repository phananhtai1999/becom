<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScenarioUuidColumnInUserUseCreditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_use_credit_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_uuid')->nullable()->change();
            $table->unsignedBigInteger('scenario_uuid')->nullable()->after('campaign_uuid');
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `user_use_credit_histories` CHANGE `type` `type` ENUM('sms','email','telegram','viber') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'email'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_use_credit_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_uuid')->nullable(false)->change();
            $table->dropColumn('scenario_uuid');
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `user_use_credit_histories` CHANGE `type` `type` ENUM('sms','email','telegram','viber') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email';");
    }
}
