<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToWebsitePageShortCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_page_short_codes', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('short_code');
            $table->dropColumn('type');
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
            $table->dropColumn('status');
            $table->string('type');
        });
    }
}
