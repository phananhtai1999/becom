<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInAssetSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_sizes', function (Blueprint $table) {
            $table->dropColumn('asset_group_code');
            $table->integer('asset_group_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_sizes', function (Blueprint $table) {
            $table->dropColumn('asset_group_uuid');
            $table->string('asset_group_code');
        });
    }
}
