<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserUuidToSmtpAccountsCampaignsEmailsMailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('website_uuid');
        });
        Schema::table('campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('website_uuid');
        });
        Schema::table('emails', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('job');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('website_uuid');
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
            $table->dropColumn('user_uuid');
        });
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
    }
}
