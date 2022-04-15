<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSocialProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_social_profiles', function (Blueprint $table) {
            $table->id('uuid');
            $table->unsignedBigInteger('user_uuid');
            $table->string('social_network_uuid');
            $table->string('social_profile_key')->unique();
            $table->json('other_data')->nullable();
            $table->string('social_profile_name')->nullable();
            $table->string('social_profile_avatar')->nullable();
            $table->string('social_profile_email')->nullable();
            $table->string('social_profile_phone')->nullable();
            $table->string('social_profile_address')->nullable();
            $table->dateTime('updated_info_at')->nullable();
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
        Schema::dropIfExists('user_social_profiles');
    }
}
