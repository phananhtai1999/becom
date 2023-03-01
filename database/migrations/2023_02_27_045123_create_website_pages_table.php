<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('title');
            $table->longText('template');
            $table->json('template_json');
            $table->unsignedBigInteger('user_uuid');
            $table->unsignedTinyInteger('publish_status')->default(2);
            $table->unsignedBigInteger('website_page_category_uuid');
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
        Schema::dropIfExists('website_pages');
    }
}
