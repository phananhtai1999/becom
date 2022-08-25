<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMailUsernameAndMailFromAddressToUniqueInSmtpAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->string('mail_username')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->dropUnique(['mail_username']);
        });
    }
}
