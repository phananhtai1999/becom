<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserUuidColumnToContactAndContactListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('country');
        });

        Schema::table('contact_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
        Schema::table('contact_lists', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
    }
}
