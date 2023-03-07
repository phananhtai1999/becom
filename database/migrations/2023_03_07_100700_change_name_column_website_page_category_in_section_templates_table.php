<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameColumnWebsitePageCategoryInSectionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->renameColumn('website_page_category_uuid', 'section_category_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->renameColumn('section_category_uuid', 'website_page_category_uuid');
        });
    }
}
