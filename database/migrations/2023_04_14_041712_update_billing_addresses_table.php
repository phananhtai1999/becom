<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBillingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->renameColumn('billing_address', 'country');
            $table->string('state')->after('company');
            $table->string('city')->after('company');
            $table->integer('zipcode')->after('company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->renameColumn('country', 'billing_address');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('zipcode');
        });
    }
}
