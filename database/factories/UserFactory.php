<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'first_name' => $this->faker->name(),
			'last_name' => $this->faker->name(),
			'username' => $this->faker->userName(),
			'email' => $this->faker->unique()->safeEmail(),
			'email_verified_at' => now(),
			'password' => Hash::make('111@222'),
            'can_add_smtp_account' => true,

		];
	}

	/**
	 * Indicate that the model's email address should be unverified.
	 *
	 * @return \Illuminate\Database\Eloquent\Factories\Factory
	 */
	public function unverified() {
		return $this->state(function (array $attributes) {
			return [
				'email_verified_at' => null,
			];
		});
	}
}
