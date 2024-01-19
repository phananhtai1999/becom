<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultDepartmentToBecomUserProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('becom_user_profiles', function (Blueprint $table) {
            $table->boolean('default_department')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('becom_user_profiles', function (Blueprint $table) {
            $table->dropColumn('default_department');
        });
    }
}
