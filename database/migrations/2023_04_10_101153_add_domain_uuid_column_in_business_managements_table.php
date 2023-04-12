<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainUuidColumnInBusinessManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_managements', function (Blueprint $table) {
            $table->unsignedBigInteger('domain_uuid')->after('owner_uuid')->nullable();
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
            $table->dropColumn('domain_uuid');
        });
    }
}
