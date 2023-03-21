<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->integer('user_uuid')->unique();
            $table->string('active_code')->nullable();
            $table->dateTime('expired_time')->nullable();
            $table->dateTime('blocked_time')->nullable();
            $table->dateTime('refresh_time')->nullable();
            $table->integer('refresh_count')->default(0);
            $table->integer('wrong_count')->default(0);
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
        Schema::dropIfExists('otps');
    }
}
