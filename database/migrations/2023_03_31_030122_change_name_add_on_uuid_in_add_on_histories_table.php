<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameAddOnUuidInAddOnHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_on_histories', function (Blueprint $table) {
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
        Schema::table('add_on_histories', function (Blueprint $table) {
            $table->renameColumn('add_on_subscription_plan_uuid', 'add_on_uuid');
        });
    }
}
