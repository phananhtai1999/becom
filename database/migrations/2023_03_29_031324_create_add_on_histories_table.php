<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddOnHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_on_histories', function (Blueprint $table) {
            $table->id('uuid');
            $table->integer('user_uuid');
            $table->integer('add_on_uuid');
            $table->dateTime('subscription_date')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->integer('payment_method_uuid');
            $table->json('logs');
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
        Schema::dropIfExists('add_on_histories');
    }
}
