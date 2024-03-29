<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionAndImageInPartnerLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_levels', function (Blueprint $table) {
            $table->string('image')->nullable()->after('uuid');
            $table->json('content')->nullable()->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_levels', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('content');
        });
    }
}
