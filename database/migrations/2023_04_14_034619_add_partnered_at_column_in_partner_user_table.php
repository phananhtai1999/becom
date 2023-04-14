<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartneredAtColumnInPartnerUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_user', function (Blueprint $table) {
            $table->dateTime('partnered_at')->nullable()->after('registered_from_partner_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_user', function (Blueprint $table) {
            $table->dropColumn('partnered_at');
        });
    }
}
