<?php

use App\Models\WebsitePage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitePageShortCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_page_short_codes', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('key')->unique();
            $table->string('name')->nullable();
            $table->string('parent_uuid')->nullable();
            $table->string('short_code');
            $table->string('type')->default(WebsitePage::HOME_ARTICLES_TYPE);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_page_short_codes');
    }
}
