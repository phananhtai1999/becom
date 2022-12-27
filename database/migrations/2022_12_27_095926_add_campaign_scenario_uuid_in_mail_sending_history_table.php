<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCampaignScenarioUuidInMailSendingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->dropColumn('campaign_uuid');
            $table->unsignedBigInteger('campaign_scenario_uuid')->after('uuid');
        });
        DB::table('mail_sending_history')->truncate();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->dropColumn('campaign_scenario_uuid');
            $table->unsignedBigInteger('campaign_uuid')->after('uuid');
        });
    }
}
