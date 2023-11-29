<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortCodeShortCodeGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_code_short_code_group', function (Blueprint $table) {
            $table->bigIncrements('uuid');
            $table->bigInteger('short_code_uuid');
            $table->bigInteger('short_code_group_uuid');
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
        Schema::dropIfExists('short_code_short_code_group');
    }
}
