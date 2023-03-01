<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMailMailerInSmtpAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->string('mail_host')->nullable()->change();
            $table->string('mail_port')->nullable()->change();
            $table->string('mail_username')->nullable()->change();
            $table->string('mail_password')->nullable()->change();
            $table->string('mail_from_address')->nullable()->change();
            $table->string('mail_from_name')->nullable()->change();
            $table->string('secret_key')->nullable()->change();
        });

        \Illuminate\Support\Facades\DB::statement("UPDATE `smtp_accounts` SET `mail_mailer` = CASE
    WHEN `mail_mailer` = 'smtp' THEN 'smtp'
    WHEN `mail_mailer` = 'telegram' THEN 'telegram'
    WHEN `mail_mailer` = 'viber' THEN 'viber'
    ELSE 'smtp'
    END");

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `smtp_accounts` CHANGE `mail_mailer` `mail_mailer` ENUM('smtp', 'telegram', 'viber') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'smtp'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->string('mail_host')->nullable(false)->change();
            $table->string('mail_port')->nullable(false)->change();
            $table->string('mail_username')->nullable(false)->change();
            $table->string('mail_password')->nullable(false)->change();
            $table->string('mail_from_address')->nullable(false)->change();
            $table->string('mail_from_name')->nullable(false)->change();
            $table->string('secret_key')->nullable(false)->change();
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `smtp_accounts` CHANGE `mail_mailer` `mail_mailer` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}
