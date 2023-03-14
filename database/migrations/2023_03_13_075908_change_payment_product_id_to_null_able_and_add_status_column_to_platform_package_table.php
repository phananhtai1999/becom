<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePaymentProductIdToNullAbleAndAddStatusColumnToPlatformPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_packages', function (Blueprint $table) {
            $table->json('payment_product_id')->nullable()->change();
            $table->enum('status', ['publish', 'disable', 'draft'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_packages', function (Blueprint $table) {
            $table->json('payment_product_id')->change();
            $table->dropColumn('status');
        });
    }
}
