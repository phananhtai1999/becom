<?php

namespace Database\Factories;

use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailTemplateFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'subject' => $this->faker->sentence(3),
			'body' => $this->faker->sentence(10),
			'design' => "{}",
			'website_uuid' => Website::where('uuid', '<=', 3)->inRandomOrder()->first()->uuid,
		];
	}
}
