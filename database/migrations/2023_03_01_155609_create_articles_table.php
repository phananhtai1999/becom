<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('article_category_uuid')->nullable();
            $table->unsignedBigInteger('user_uuid');
            $table->string('image')->nullable();
            $table->string('slug');
            $table->json('title');
            $table->json('content');
            $table->unsignedTinyInteger('publish_status')->default(2);
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
        Schema::dropIfExists('articles');
    }
}
