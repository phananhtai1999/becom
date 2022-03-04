<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignLinkTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_link_trackings', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('campaign_uuid');
            $table->unsignedInteger('to_url');
            $table->unsignedInteger('total_click');
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
        Schema::dropIfExists('campaign_link_trackings');
    }
}
