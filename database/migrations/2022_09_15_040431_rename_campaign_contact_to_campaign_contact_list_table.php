<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCampaignContactToCampaignContactListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('campaign_contact', 'campaign_contact_list');

        Schema::table('campaign_contact_list', function (Blueprint $table) {
            $table->renameColumn('contact_uuid', 'contact_list_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('campaign_contact_list', 'campaign_contact');

        Schema::table('campaign_contact', function (Blueprint $table) {
            $table->renameColumn('contact_list_uuid', 'contact_uuid');
        });
    }
}
