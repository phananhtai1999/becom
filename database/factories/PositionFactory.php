<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => [
                'en' => $this->faker->name(2),
                'vi' => $this->faker->name(2)
            ],
            'user_uuid' => User::inRandomOrder()->first()->uuid,
        ];
    }
}
