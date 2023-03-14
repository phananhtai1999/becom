<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailSendingHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $optionType = array(
            'sent',
            'opened',
            'fail',
            'received'
        );
        return [
            'campaign_uuid' => Campaign::inRandomOrder()->first()->uuid,
            'campaign_scenario_uuid' => null,
            'email' => $this->faker->safeEmail(),
            'time' => $this->faker->dateTime(),
            'status' => $optionType[array_rand($optionType)],
        ];
    }
}
