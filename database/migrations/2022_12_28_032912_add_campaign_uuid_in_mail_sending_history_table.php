<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignUuidInMailSendingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_scenario_uuid')->nullable()->change();
            $table->unsignedBigInteger('campaign_uuid')->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->dropColumn('campaign_uuid');
        });
    }
}
