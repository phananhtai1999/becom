<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RemindFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'note' => $this->faker->text(50),
            'date' => $this->faker->dateTime(),
            'user_uuid' => User::inRandomOrder()->first()->uuid,
        ];
    }
}
