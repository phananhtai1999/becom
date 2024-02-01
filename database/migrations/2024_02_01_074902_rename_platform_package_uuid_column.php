<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePlatformPackageUuidColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_ons', function (Blueprint $table) {
            $table->renameColumn('platform_package_uuid', 'app_uuid');
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->renameColumn('platform_package_uuid', 'app_uuid');
        });

        Schema::table('user_app', function (Blueprint $table) {
            $table->renameColumn('platform_package_uuid', 'app_uuid');
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
            $table->renameColumn('app_uuid', 'platform_package_uuid');
        });
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->renameColumn('app_uuid', 'platform_package_uuid');
        });

        Schema::table('user_app', function (Blueprint $table) {
            $table->renameColumn('app_uuid', 'platform_package_uuid');
        });
    }
}
