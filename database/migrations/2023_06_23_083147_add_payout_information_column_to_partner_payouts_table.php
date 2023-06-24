<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayoutInformationColumnToPartnerPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->integer('payout_information_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->dropColumn('payout_information_uuid');
        });
    }
}
