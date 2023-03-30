<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePaymentPlanIdToNullableInAddOnSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_on_subscription_plans', function (Blueprint $table) {
            $table->json('payment_plan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_on_subscription_plans', function (Blueprint $table) {
            $table->json('payment_plan_id')->change();
        });
    }
}
