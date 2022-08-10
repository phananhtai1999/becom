<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEndTimeAndLogToNullableInSendEmailScheduleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('send_email_schedule_logs', function (Blueprint $table) {
            $table->dateTime('end_time')->nullable()->change();
            $table->text('log')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('send_email_schedule_logs', function (Blueprint $table) {
            $table->dateTime('end_time')->change();
            $table->text('log')->change();
        });
    }
}
