<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeStatusOfMailSendingHistory extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
        DB::statement("ALTER TABLE `mail_sending_history`
        CHANGE `status` `status` enum('sent','fail','received','opened','processing') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'processing' AFTER `time`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->enum('status', ['sent', 'fail', 'received', 'opened'])
                ->default('sent')
                ->change();
        });
	}
}
