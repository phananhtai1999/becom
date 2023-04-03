<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayTypeColoumInWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->enum('display_type', ['page', 'in_page'])->default('page')->after('is_default');
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
            $table->dropColumn('display_type');
        });
    }
}
