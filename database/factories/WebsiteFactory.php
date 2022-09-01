<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'domain' => $this->faker->domainWord() .'.' . $this->faker->domainWord() .'.'. $this->faker->domainName(),
			'user_uuid' => rand(1, 3),
			'name' => $this->faker->name(),
			'description' => $this->faker->sentence(3),
			'logo' => $this->faker->imageUrl(100, 100, 'animals', true),
		];
	}
}
