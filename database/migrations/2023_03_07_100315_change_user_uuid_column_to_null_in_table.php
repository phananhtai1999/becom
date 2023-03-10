<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserUuidColumnToNullInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable()->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable()->change();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable(false)->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable(false)->change();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable(false)->change();
        });
    }
}
