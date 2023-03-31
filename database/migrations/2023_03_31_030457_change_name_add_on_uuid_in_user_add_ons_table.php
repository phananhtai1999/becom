<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameAddOnUuidInUserAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_add_ons', function (Blueprint $table) {
            $table->renameColumn('add_on_uuid', 'add_on_subscription_plan_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_add_ons', function (Blueprint $table) {
            $table->renameColumn('add_on_subscription_plan_uuid', 'add_on_uuid');
        });
    }
}
