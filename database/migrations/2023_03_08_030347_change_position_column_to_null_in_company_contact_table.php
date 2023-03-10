<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePositionColumnToNullInCompanyContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_contact', function (Blueprint $table) {
            $table->dropPrimary('company_contact');
        });

        Schema::table('company_contact', function (Blueprint $table) {
            $table->unsignedBigInteger('position_uuid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_contact', function (Blueprint $table) {
            $table->primary(['contact_uuid', 'company_uuid', 'position_uuid']);
        });
    }
}
