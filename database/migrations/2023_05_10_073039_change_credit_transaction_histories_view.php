<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeCreditTransactionHistoriesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->dropView());
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function createView(): string
    {
        return <<<SQL
            CREATE VIEW `transactions` AS
              (SELECT `uuid`,
                      `user_uuid`,
                      `credit`,
                      NULL AS campaign_uuid,
                      NULL AS scenario_uuid,
                      `add_by_uuid`,
                      `created_at`
               FROM `user_credit_histories`
               WHERE `user_credit_histories`.`deleted_at` IS NULL)
            UNION ALL
              (SELECT `uuid`,
                      `user_uuid`,
                      `credit`,
                      `campaign_uuid`,
                      `scenario_uuid`,
                      NULL AS add_by_uuid,
                      `created_at`
               FROM `user_use_credit_histories`
               WHERE `user_use_credit_histories`.`deleted_at` IS NULL
               ORDER BY `created_at` DESC);
            SQL;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function dropView(): string
    {
        return <<<SQL

            DROP VIEW IF EXISTS `transactions`;
            SQL;
    }
}
