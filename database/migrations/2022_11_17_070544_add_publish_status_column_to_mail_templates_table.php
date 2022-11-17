<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishStatusColumnToMailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->unsignedTinyInteger('publish_status')->after('design');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn('publish_status');
        });
    }
}
