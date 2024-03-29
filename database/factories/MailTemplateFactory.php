<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SendProject;
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
            'telegram',
            'viber'
        );
		return [
			'subject' => $this->faker->sentence(3),
			'body' => $this->faker->sentence(10),
			'design' => "{}",
			'send_project_uuid' => optional(SendProject::inRandomOrder()->first())->uuid,
            "user_uuid" => User::inRandomOrder()->first()->uuid,
            'app_id' => $this->faker->sentence(3),
            "publish_status" => true,
            "type" => $optionType[array_rand($optionType)],
		];
	}
}
