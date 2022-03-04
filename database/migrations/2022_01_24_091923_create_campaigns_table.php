<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('tracking_key');
            $table->unsignedBigInteger('mail_template_uuid');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->unsignedInteger('number_email_per_date');
            $table->unsignedInteger('number_email_per_user');
            $table->string('status');
            $table->unsignedBigInteger('smtp_account_uuid');
            $table->unsignedBigInteger('website_uuid');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
