<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactUuidColumnToActivityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_uuid')->after('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_histories', function (Blueprint $table) {
            $table->dropColumn('contact_uuid');
        });
    }
}
