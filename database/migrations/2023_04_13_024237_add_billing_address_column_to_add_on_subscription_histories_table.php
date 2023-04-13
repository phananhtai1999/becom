<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingAddressColumnToAddOnSubscriptionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_on_subscription_histories', function (Blueprint $table) {
            $table->integer('billing_address_uuid')->after('logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_on_subscription_histories', function (Blueprint $table) {
            $table->dropColumn('billing_address_uuid');
        });
    }
}
