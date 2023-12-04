<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppIdAndChangeUserUuidToStringToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getTables() as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('user_uuid')->change();
                $table->string('app_id')->after('user_uuid')->nullable();
            });
        }

        foreach ($this->getTablesHaveUserUuidCanNull() as $value) {
            Schema::table($value, function (Blueprint $value) {
                $value->string('user_uuid')->nullable()->change();
                $value->string('app_id')->after('user_uuid')->nullable();
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->string('app_id')->nullable()->after('owner_uuid');
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->string('app_id')->nullable()->after('owner_uuid');
        });
        Schema::table('business_managements', function (Blueprint $table) {
            $table->string('app_id')->nullable()->after('owner_uuid');
        });
        Schema::table('article_series', function (Blueprint $table) {
            $table->string('app_id')->nullable()->after('assigned_ids');
        });
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->string('app_id')->nullable()->after('by_user_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        foreach ($this->getTables() as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('user_uuid')->change();
                $table->dropColumn('app_id');
            });
        }
        foreach ($this->getTablesHaveUserUuidCanNull() as $value) {
            Schema::table($value, function (Blueprint $value) {
                $value->unsignedBigInteger('user_uuid')->nullable()->change();
                $value->dropColumn('app_id');
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
        Schema::table('business_managements', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
        Schema::table('article_series', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
        Schema::table('partner_payouts', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
    }

    private function getTablesHaveUserUuidCanNull()
    {
        return [
            'status',
            'companies',
            'positions',
            'websites',
            'partners'
        ];
    }

    private function getTables()
    {
        return [
            'add_on_subscription_histories',
            'articles',
            'article_categories',
            'assets',
            'billing_addresses',
            'campaigns',
            'contacts',
            'contact_lists',
            'credit_package_histories',
            'departments',
            'emails',
            'footer_templates',
            'forms',
            'invoices',
            'locations',
            'mail_templates',
            'notes',
            'notifications',
            'orders',
            'otps',
            'paragraph_types',
            'partner_user',
            'payout_methods',
            'reminds',
            'role_user',
            'scenarios',
            'section_templates',
            'send_projects',
            'single_purposes',
            'smtp_accounts',
            'subscription_histories',
            'user_access_tokens',
            'user_add_ons',
            'user_business',
            'user_configs',
            'user_credit_histories',
            'user_details',
            'user_payment_by_day',
            'user_platform_package',
            'user_social_profiles',
            'user_teams',
            'user_team_contact_lists',
            'user_trackings',
            'user_use_credit_histories',
            'website_pages'
        ];
    }
}
