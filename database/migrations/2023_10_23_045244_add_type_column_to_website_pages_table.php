<?php

use App\Models\WebsitePage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnToWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->string('type')->after('user_uuid')->default(WebsitePage::STATIC_TYPE);
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
            $table->dropColumn('type');
        });
    }
}
