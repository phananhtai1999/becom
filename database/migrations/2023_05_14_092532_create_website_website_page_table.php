<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteWebsitePageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_website_page', function (Blueprint $table) {
            $table->unsignedBigInteger('website_uuid');
            $table->unsignedBigInteger('website_page_uuid');
            $table->primary(['website_uuid', 'website_page_uuid']);
            $table->boolean('is_homepage')->default(false);
            $table->unsignedInteger('ordering')->nullable();
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
        Schema::dropIfExists('website_website_page');
    }
}
