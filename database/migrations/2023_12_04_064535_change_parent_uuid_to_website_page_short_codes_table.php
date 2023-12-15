<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeParentUuidToWebsitePageShortCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_page_short_codes', function (Blueprint $table) {
            $table->json('parent_uuids')->nullable();
            $table->dropColumn('parent_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_page_short_codes', function (Blueprint $table) {
            $table->dropColumn('parent_uuid');
            $table->integer('parent_uuid')->nullable();
        });
    }
}
