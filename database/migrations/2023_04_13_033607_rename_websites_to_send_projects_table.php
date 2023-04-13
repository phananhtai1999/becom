<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameWebsitesToSendProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('websites', 'send_projects');
        Schema::table('campaigns', function (Blueprint $table) {
            $table->renameColumn('website_uuid', 'send_project_uuid');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->renameColumn('website_uuid', 'send_project_uuid');
        });
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->renameColumn('website_uuid', 'send_project_uuid');
        });
        Schema::table('website_email', function (Blueprint $table) {
            $table->renameColumn('website_uuid', 'send_project_uuid');
        });
        Schema::table('website_verifications', function (Blueprint $table) {
            $table->renameColumn('website_uuid', 'send_project_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('send_projects', 'websites');
        Schema::table('campaigns', function (Blueprint $table) {
            $table->renameColumn('send_project_uuid', 'website_uuid');
        });
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->renameColumn('send_project_uuid', 'website_uuid');
        });
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->renameColumn('send_project_uuid', 'website_uuid');
        });
        Schema::table('website_email', function (Blueprint $table) {
            $table->renameColumn('send_project_uuid', 'website_uuid');
        });
        Schema::table('website_verifications', function (Blueprint $table) {
            $table->renameColumn('send_project_uuid', 'website_uuid');
        });
    }
}
