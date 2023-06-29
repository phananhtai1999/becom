<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('content_type', ['single','paragraph'])->default('single')->after('content_for_user');
            $table->unsignedBigInteger('single_purpose_uuid')->nullable()->after('content_type');
            $table->unsignedBigInteger('paragraph_type_uuid')->nullable()->after('single_purpose_uuid');
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
            $table->dropColumn('content_type');
            $table->dropColumn('single_purpose_uuid');
            $table->dropColumn('paragraph_type_uuid');
        });
    }
}
