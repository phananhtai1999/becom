<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentUuidToSendProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('send_projects', function (Blueprint $table) {
            $table->bigInteger('parent_uuid')->nullable()->after('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('send_projects', function (Blueprint $table) {
            $table->dropColumn('parent_uuid');
        });
    }
}
