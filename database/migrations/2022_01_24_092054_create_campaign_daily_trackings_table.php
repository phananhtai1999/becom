<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignDailyTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_daily_trackings', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('campaign_uuid');
            $table->unsignedInteger('total_open');
            $table->unsignedInteger('total_link_click');
            $table->date('date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_daily_trackings');
    }
}
