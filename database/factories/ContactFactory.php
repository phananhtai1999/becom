<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $optionType = array(
            'Men',
            'Women',
        );
        return [
            'email' => $this->faker->safeEmail(),
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'middle_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'dob' => $this->faker->date(),
            'sex' => $optionType[array_rand($optionType)],
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'user_uuid' => User::inRandomOrder()->first()->uuid,
        ];
    }
}
