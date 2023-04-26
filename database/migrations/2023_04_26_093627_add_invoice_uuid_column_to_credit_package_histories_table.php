<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceUuidColumnToCreditPackageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_package_histories', function (Blueprint $table) {
            $table->integer('invoice_uuid')->after('logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_package_histories', function (Blueprint $table) {
            $table->dropColumn('invoice_uuid');
        });
    }
}
