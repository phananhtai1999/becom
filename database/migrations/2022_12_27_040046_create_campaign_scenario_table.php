<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignScenarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_scenario', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('campaign_uuid');
            $table->unsignedBigInteger('scenario_uuid');
            $table->unsignedBigInteger('parent_uuid')->nullable();
            $table->integer('left')->nullable();
            $table->integer('right')->nullable();
            $table->integer('depth')->nullable();
            $table->enum('type', ['open', 'not_open'])->default('open');
            $table->integer('open_within')->nullable();
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
        Schema::dropIfExists('campaign_scenario');
    }
}
