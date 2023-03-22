<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeColumnInFooterTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('footer_templates', function (Blueprint $table) {
            $table->enum('template_type', ['ads', 'subscribe'])->default('ads')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('footer_templates', function (Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
}
