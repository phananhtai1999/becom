<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCanAddSmtpAccountColumnInBecomUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE becom_user_profiles CHANGE COLUMN can_add_smtp_account can_add_smtp_account TINYINT UNSIGNED NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('becom_user_profiles', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE becom_user_profiles CHANGE COLUMN can_add_smtp_account can_add_smtp_account TINYINT UNSIGNED NOT NULL DEFAULT 1");
        });
    }
}
