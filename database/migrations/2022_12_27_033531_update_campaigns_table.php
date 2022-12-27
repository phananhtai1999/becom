<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('number_email_per_date');
            $table->dropColumn('number_email_per_user');
            $table->dropColumn('open_within');
            $table->dropColumn('open_mail_campaign');
            $table->dropColumn('not_open_mail_campaign');

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
            $table->unsignedInteger('number_email_per_date')->after('mail_template_uuid');
            $table->unsignedInteger('number_email_per_user')->after('number_email_per_date');
            $table->integer('open_within')->nullable()->after('was_stopped_by_owner');
            $table->unsignedBigInteger('open_mail_campaign')->nullable()->after('open_within');
            $table->unsignedBigInteger('not_open_mail_campaign')->nullable()->after('open_mail_campaign');
        });
    }
}
