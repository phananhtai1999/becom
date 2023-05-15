<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitesManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('title');
            $table->unsignedBigInteger('domain_uuid')->nullable();
            $table->unsignedBigInteger('header_section_uuid')->nullable();
            $table->unsignedBigInteger('footer_section_uuid')->nullable();
            $table->unsignedBigInteger('user_uuid')->nullable();
            $table->unsignedTinyInteger('publish_status')->default(2);
            $table->string('logo')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('websites');
    }
}
