<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTotalCommissionToFloatInPartTrackingByYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_tracking_by_year', function (Blueprint $table) {
            $table->unsignedFloat('total_commission')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_tracking_by_year', function (Blueprint $table) {
            $table->unsignedInteger('total_commission')->change();
        });
    }
}
