<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToNullableInArticlesAndArticleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->json('keyword')->nullable()->change();
        });
        Schema::table('article_categories', function (Blueprint $table) {
            $table->json('keyword')->nullable()->change();
            $table->string('feature_image')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->json('keyword')->nullable(false)->change();
        });
        Schema::table('article_categories', function (Blueprint $table) {
            $table->json('keyword')->nullable(false)->change();
        });
    }
}
