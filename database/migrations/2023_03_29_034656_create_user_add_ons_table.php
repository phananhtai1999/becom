<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_add_ons', function (Blueprint $table) {
            $table->id('uuid');
            $table->integer('user_uuid');
            $table->string('add_on_uuid');
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
        Schema::dropIfExists('user_add_ons');
    }
}
