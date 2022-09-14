<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteUniqueDatabaseEmailsAndSmtpAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->dropUnique(['mail_username']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->string('mail_username')->unique()->change();
        });
    }
}
