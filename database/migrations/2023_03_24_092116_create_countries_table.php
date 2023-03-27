<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('national_flag');
            $table->string('country_code');
            $table->string('name');
            $table->string('country_phone_code');
            $table->integer('sms_price')->nullable();
            $table->integer('email_price')->nullable();
            $table->integer('telegram_price')->nullable();
            $table->integer('viber_price')->nullable();
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
        Schema::dropIfExists('countries');
    }
}
