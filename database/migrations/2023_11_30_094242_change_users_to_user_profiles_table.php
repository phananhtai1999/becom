<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsersToUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('users', 'user_profiles');

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['username', 'first_name', 'last_name', 'email', 'password']);
            $table->string('app_id')->nullable();
            $table->string('user_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('user_profiles', 'users');

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('password');
            $table->dropColumn(['user_uuid', 'app_id']);
        });
    }
}
