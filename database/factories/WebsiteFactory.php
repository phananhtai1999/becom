<?php

namespace Database\Factories;

use App\Models\User;
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
			'user_uuid' => User::inRandomOrder()->first()->uuid,
			'name' => $this->faker->name(),
			'description' => $this->faker->sentence(3),
			'logo' => $this->faker->imageUrl(100, 100, 'animals', true),
		];
	}
}
