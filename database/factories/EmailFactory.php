<?php

namespace Database\Factories;

use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'email' => $this->faker->safeEmail(),
			'age' => rand(20, 55),
			'first_name' => $this->faker->name(),
			'last_name' => $this->faker->name(),
			'country' => 'VN',
			'city' => 'Ho Chi Minh',
			'job' => $this->faker->jobTitle(),
			'website_uuid' => Website::where('uuid', '<=', 3)->inRandomOrder()->first()->uuid,
		];
	}
}
