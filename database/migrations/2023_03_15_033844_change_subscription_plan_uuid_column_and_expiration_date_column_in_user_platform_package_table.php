<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSubscriptionPlanUuidColumnAndExpirationDateColumnInUserPlatformPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_platform_package', function (Blueprint $table) {
            $table->integer('subscription_plan_uuid')->nullable()->change();
            $table->dateTime('expiration_date')->nullable()->change();
            $table->boolean('auto_renew')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_platform_package', function (Blueprint $table) {
            $table->integer('subscription_plan_uuid')->change();
            $table->dateTime('expiration_date')->change();
            $table->boolean('auto_renew')->change();
        });
    }
}
