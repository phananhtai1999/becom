<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUserTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->renameColumn('country', 'last_login_location');
            $table->string('register_location')->nullable()->after('ip');
        });

        DB::statement("UPDATE user_trackings SET register_location = last_login_location");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->renameColumn('last_login_location', 'country');
            $table->dropColumn('register_location');
        });
    }
}
