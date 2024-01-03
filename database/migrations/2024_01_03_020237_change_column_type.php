<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('owner_uuid')->change();
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->string('owner_uuid')->change();
        });
        Schema::table('business_managements', function (Blueprint $table) {
            $table->string('owner_uuid')->change();
        });
        Schema::table('footer_templates', function (Blueprint $table) {
            $table->string('active_by_uuid')->change();
        });
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->string('by_user_uuid')->change();
        });
        Schema::table('article_series', function (Blueprint $table) {
            $table->string('assigned_ids')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_uuid')->change();
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_uuid')->change();
        });
        Schema::table('business_managements', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_uuid')->change();
        });
        Schema::table('footer_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('active_by_uuid')->change();
        });
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->unsignedBigInteger('by_user_uuid')->change();
        });
        Schema::table('article_series', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_ids')->change();
        });
    }
}
