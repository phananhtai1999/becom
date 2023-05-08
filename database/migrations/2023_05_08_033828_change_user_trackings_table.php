<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeUserTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->renameColumn('register_location', 'location');
            $table->dropColumn('last_login_location');
            $table->string('user_agent')->after('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->renameColumn('location', 'register_location');
            $table->string('last_login_location');
            $table->dropColumn('user_agent');
        });
        DB::statement("UPDATE user_trackings SET register_location = last_login_location");
    }
}
