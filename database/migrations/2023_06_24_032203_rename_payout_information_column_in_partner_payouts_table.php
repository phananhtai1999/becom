<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePayoutInformationColumnInPartnerPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->renameColumn('payout_information_uuid', 'payout_method_uuid');
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
            $table->renameColumn('payout_method_uuid', 'payout_information_uuid');
        });
    }
}
