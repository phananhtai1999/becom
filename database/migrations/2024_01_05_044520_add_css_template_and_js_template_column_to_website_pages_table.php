<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCssTemplateAndJsTemplateColumnToWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->longText('js_template')->after('template');
            $table->longText('css_template')->after('template');
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
            $table->dropColumn('js_template');
            $table->dropColumn('css_template');
        });
    }
}
