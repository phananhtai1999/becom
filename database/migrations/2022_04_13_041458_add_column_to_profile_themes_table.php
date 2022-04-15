<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProfileThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_themes', function (Blueprint $table) {
            $table->string('preview_img')->after('files');
            $table->decimal('price')->after('preview_img');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_themes', function (Blueprint $table) {
            $table->dropColumn('preview_img');
            $table->dropColumn('price');
        });
    }
}
