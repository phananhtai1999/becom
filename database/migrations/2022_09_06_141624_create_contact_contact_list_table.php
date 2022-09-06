<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactContactListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_contact_list', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_uuid');
            $table->unsignedBigInteger('contact_list_uuid');
            $table->primary(['contact_uuid', 'contact_list_uuid']);
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
        Schema::dropIfExists('contact_contact_list');
    }
}
