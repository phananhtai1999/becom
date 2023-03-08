<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsDefaultInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->boolean('is_default')->default(0)->after('publish_status');
        });
        Schema::table('section_templates', function (Blueprint $table) {
            $table->boolean('is_default')->default(0)->after('publish_status');
        });
        Schema::table('forms', function (Blueprint $table) {
            $table->unsignedTinyInteger('publish_status')->default(2)->after('title');
            $table->unsignedBigInteger('contact_list_uuid')->nullable()->change();
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
            $table->dropColumn('is_default');
        });
        Schema::table('section_templates', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('publish_status');
            $table->unsignedBigInteger('contact_list_uuid')->nullable(false)->change();
        });
    }
}
