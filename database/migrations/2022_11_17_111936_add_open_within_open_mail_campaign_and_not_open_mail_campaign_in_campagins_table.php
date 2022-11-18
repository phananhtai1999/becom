<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpenWithinOpenMailCampaignAndNotOpenMailCampaignInCampaginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->integer('open_within')->nullable()->after('was_stopped_by_owner');
            $table->unsignedBigInteger('open_mail_campaign')->nullable()->after('open_within');
            $table->unsignedBigInteger('not_open_mail_campaign')->nullable()->after('open_mail_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('open_within');
            $table->dropColumn('open_mail_campaign');
            $table->dropColumn('not_open_mail_campaign');
        });
    }
}
