<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailTemplateFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
        $optionType = array(
            'email',
            'sms',
        );
		return [
			'subject' => $this->faker->sentence(3),
			'body' => $this->faker->sentence(10),
			'design' => "{}",
			'website_uuid' => Website::inRandomOrder()->first()->uuid,
            "user_uuid" => User::inRandomOrder()->first()->uuid,
            "publish_status" => true,
            "type" => $optionType[array_rand($optionType)]
		];
	}
}
