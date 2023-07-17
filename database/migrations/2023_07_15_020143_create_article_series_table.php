<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_series', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('slug');
            $table->json('title');
            $table->unsignedBigInteger('article_category_uuid')->nullable();
            $table->string('list_keywords')->nullable();
            $table->unsignedBigInteger('parent_uuid')->nullable();
            $table->integer('left')->nullable();
            $table->integer('right')->nullable();
            $table->integer('depth')->nullable();
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
        Schema::dropIfExists('article_series');
    }
}
