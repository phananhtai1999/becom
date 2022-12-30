<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeToNullableInCampaignScenarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_scenario', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('campaign_scenario', function (Blueprint $table) {
            $table->enum('type', ['open', 'not_open'])->nullable()->after('depth');
        });
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
