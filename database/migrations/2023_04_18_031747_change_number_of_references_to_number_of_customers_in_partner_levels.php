<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNumberOfReferencesToNumberOfCustomersInPartnerLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_levels', function (Blueprint $table) {
            $table->renameColumn('number_of_references', 'number_of_customers');
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
            $table->renameColumn('number_of_customers', 'number_of_references');
        });
    }
}
