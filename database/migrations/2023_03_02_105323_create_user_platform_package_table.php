<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlatformPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_platform_package', function (Blueprint $table) {
            $table->id('uuid');
            $table->integer('user_uuid');
            $table->string('platform_package_uuid');
            $table->integer('subscription_plan_uuid');
            $table->dateTime('expiration_date');
            $table->boolean('auto_renew');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_platform_package');
    }
}
