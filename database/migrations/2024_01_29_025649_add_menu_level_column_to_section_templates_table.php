<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuLevelColumnToSectionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->integer('menu_level')->after('type')->nullable();
            $table->string('display_mode')->after('type')->nullable();
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
            $table->dropColumn('menu_level');
            $table->dropColumn('display_mode');
        });
    }
}
