<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndPublishColumnInSmtpAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('website_uuid')->nullable()->change();
            $table->string('status')->default('work')->after('website_uuid');
            $table->boolean('publish')->default(1)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smtp_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('website_uuid')->nullable(false)->change();
            $table->dropColumn('status');
            $table->dropColumn('publish');
        });
    }
}
