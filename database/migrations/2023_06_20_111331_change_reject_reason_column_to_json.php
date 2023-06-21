<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRejectReasonColumnToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new \Database\Seeders\ClearRejectReasonSeeder())->run();
        Schema::table('website_pages', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
        Schema::table('forms', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
        Schema::table('section_templates', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->json('reject_reason')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        (new \Database\Seeders\ClearRejectReasonSeeder())->run();
        Schema::table('website_pages', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
        Schema::table('forms', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
        Schema::table('section_templates', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->text('reject_reason')->change();
        });
    }
}
