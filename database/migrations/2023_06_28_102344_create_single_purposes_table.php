<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSinglePurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('single_purposes', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('slug');
            $table->json('title');
            $table->unsignedBigInteger('user_uuid');
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
        Schema::dropIfExists('single_purposes');
    }
}
