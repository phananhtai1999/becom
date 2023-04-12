<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('partners', 'publish_status'))
        {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }
        Schema::table('partners', function (Blueprint $table) {
            $table->unsignedBigInteger('user_uuid')->nullable()->after('work_email');
            $table->unsignedBigInteger('partner_level_uuid')->nullable()->after('partner_category_uuid');
            $table->string('code')->nullable()->after('answer');
            $table->enum('publish_status', ['active', 'block', 'pending'])->default('active')->after('partner_level_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
            $table->dropColumn('partner_level_uuid');
            $table->dropColumn('code');
            $table->dropColumn('publish_status');
        });
    }
}
