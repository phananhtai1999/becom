<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusInMailSendingHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->enum('status', ['sent', 'fail', 'received', 'opened'])->default('sent')->after('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_sending_history', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
