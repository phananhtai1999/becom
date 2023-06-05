<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarAndSloganInBusinessManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_managements', function (Blueprint $table) {
            $table->string('avatar')->after('customers');
            $table->string('slogan')->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_managements', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('slogan');
        });
    }
}
