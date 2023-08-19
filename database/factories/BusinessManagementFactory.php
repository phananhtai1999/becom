<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessManagementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(10),
            'introduce' => $this->faker->text(10),
            'products_services' => [
                'products' => [$this->faker->text(10)],
                'services' => [$this->faker->text(10)],
            ],
            'customers' => [$this->faker->text(10)],
            'avatar' => $this->faker->text(10),
            'slogan' => $this->faker->text(50),
            'owner_uuid' => User::inRandomOrder()->first()->uuid,
            'domain_uuid' => Domain::inRandomOrder()->first()->uuid,
        ];
    }
}
