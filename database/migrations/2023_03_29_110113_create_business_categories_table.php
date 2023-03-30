<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id('uuid');
            $table->json('title');
            $table->unsignedTinyInteger('publish_status')->default(1);
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
        Schema::dropIfExists('business_categories');
    }
}
