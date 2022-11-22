<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnInUserUseCreditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_use_credit_histories', function (Blueprint $table) {
            $table->enum('type', ['sms', 'email'])->default('email')->after('credit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_use_credit_histories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
