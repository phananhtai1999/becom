<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactBusinessCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_business_category', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_uuid');
            $table->unsignedBigInteger('business_category_uuid');
            $table->primary(['contact_uuid', 'business_category_uuid'], 'contact_uuid_category_uuid');
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
        Schema::dropIfExists('contact_business_category');
    }
}
