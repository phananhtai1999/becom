<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserUseCreditHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_use_credit_history', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('user_uuid');
            $table->integer('credit');
            $table->unsignedBigInteger('campaign_uuid');
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
        Schema::dropIfExists('user_use_credit_history');
    }
}
