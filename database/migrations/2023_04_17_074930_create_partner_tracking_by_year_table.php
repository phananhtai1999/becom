<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerTrackingByYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_tracking_by_year', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('partner_uuid');
            $table->json('commission')->nullable();
            $table->unsignedInteger('total_commission')->nullable();
            $table->unsignedInteger('year');
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
        Schema::dropIfExists('partner_tracking_by_year');
    }
}
