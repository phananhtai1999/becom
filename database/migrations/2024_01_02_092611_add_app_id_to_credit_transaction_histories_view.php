<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAppIdToCreditTransactionHistoriesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER VIEW transactions AS
            (SELECT uuid, user_uuid, credit, NULL AS campaign_uuid, add_by_uuid, created_at, NULL AS app_id
             FROM user_credit_histories
             WHERE user_credit_histories.deleted_at IS NULL)
            UNION ALL
            (SELECT uuid, user_uuid, credit, campaign_uuid, NULL AS add_by_uuid, created_at, NULL AS app_id
             FROM user_use_credit_histories
             WHERE user_use_credit_histories.deleted_at IS NULL
             ORDER BY created_at DESC)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER VIEW transactions AS
            (SELECT uuid, user_uuid, credit, NULL AS campaign_uuid, add_by_uuid, NULL AS app_id, created_at
             FROM user_credit_histories
             WHERE user_credit_histories.deleted_at IS NULL)
            UNION ALL
            (SELECT uuid, user_uuid, credit, campaign_uuid, NULL AS add_by_uuid, NULL AS app_id, created_at
             FROM user_use_credit_histories
             WHERE user_use_credit_histories.deleted_at IS NULL
             ORDER BY created_at DESC)");
    }
}
