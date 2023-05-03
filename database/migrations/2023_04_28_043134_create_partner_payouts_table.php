<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payouts', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('partner_uuid');
            $table->enum('status', ['new', 'reject', 'accept'])->default('new');
            $table->unsignedFloat('amount')->nullable();
            $table->unsignedBigInteger('by_user_uuid')->nullable();
            $table->dateTime('time')->nullable();
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
        Schema::dropIfExists('partner_payouts');
    }
}
