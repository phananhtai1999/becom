<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentByDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_by_day', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('user_uuid');
            $table->json('payment')->nullable();
            $table->unsignedInteger('total_payment')->nullable();
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
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
        Schema::dropIfExists('user_payment_by_day');
    }
}
