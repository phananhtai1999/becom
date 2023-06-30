<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDataTypeInUserAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_add_ons', function (Blueprint $table) {
            $table->integer('add_on_subscription_plan_uuid')->change();
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
            $table->string('add_on_subscription_plan_uuid')->change();
        });
    }
}
