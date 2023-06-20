<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectReasonForTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('publish_status');
        });
        Schema::table('forms', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('publish_status');
        });
        Schema::table('section_templates', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('publish_status');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('publish_status');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('publish_status');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
        Schema::table('forms', function (Blueprint $table) {
             $table->dropColumn('reject_reason');
        });
        Schema::table('section_templates', function (Blueprint $table) {
             $table->dropColumn('reject_reason');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
             $table->dropColumn('reject_reason');
        });
        Schema::table('articles', function (Blueprint $table) {
             $table->dropColumn('reject_reason');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
    }
}
