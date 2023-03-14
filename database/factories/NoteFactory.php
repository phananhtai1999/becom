<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'note' => $this->faker->text(50),
            'contact_uuid' => Contact::inRandomOrder()->first()->uuid,
            'user_uuid' => User::inRandomOrder()->first()->uuid
        ];
    }
}
