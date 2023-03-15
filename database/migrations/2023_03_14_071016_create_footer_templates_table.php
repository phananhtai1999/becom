<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFooterTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footer_templates', function (Blueprint $table) {
            $table->id('uuid');
            $table->string('title');
            $table->longText('template');
            $table->json('template_json');
            $table->enum('type', ['email', 'sms', 'telegram', 'viber'])->default('email');
            $table->unsignedTinyInteger('publish_status')->default(2);
            $table->boolean('is_default')->default(0);
            $table->unsignedBigInteger('user_uuid');
            $table->unsignedBigInteger('active_by_uuid')->nullable();
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
        Schema::dropIfExists('footer_templates');
    }
}
