<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceInAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_ons', function (Blueprint $table) {
            $table->renameColumn('price', 'monthly');
            $table->renameColumn('payment_id', 'payment_product_id');
            $table->integer('yearly')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_ons', function (Blueprint $table) {
            $table->renameColumn('monthly', 'price');
            $table->renameColumn('payment_product_id', 'payment_id');
            $table->dropColumn('yearly');
        });
    }
}
