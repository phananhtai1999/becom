<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTemplateColumnInSectionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->renameColumn('template', 'html_template');
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
        Schema::table('section_templates', function (Blueprint $table) {
            $table->renameColumn('html_template', 'template');
            $table->dropColumn('js_template');
            $table->dropColumn('css_template');
        });
    }
}
