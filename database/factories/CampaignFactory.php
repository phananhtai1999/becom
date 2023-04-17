<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SendProject;
use App\Models\MailTemplate;
use App\Models\SmtpAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $optionType = [
            'simple',
            'birthday',
            'scenario'
        ];
        $optionSendType = [
            'email',
            'sms'
        ];
        return [
            'tracking_key' => $this->faker->lexify('tracking-??????'),
            'mail_template_uuid' => MailTemplate::inRandomOrder()->first()->uuid,
            'from_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'to_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'smtp_account_uuid' => SmtpAccount::inRandomOrder()->first()->uuid,
            'was_finished' => rand(0, 1),
            'status' => 'active',
            'was_stopped_by_owner' => rand(0, 1),
            'type' => $optionType[array_rand($optionType)],
            'send_type' => $optionSendType[array_rand($optionSendType)],
            'send_project_uuid' => SendProject::where('uuid', '<=', 3)->inRandomOrder()->first()->uuid,
            'user_uuid' => User::inRandomOrder()->first()->uuid,
            'reply_to_email' => $this->faker->safeEmail(),
            'reply_name' => $this->faker->name(),
            'send_from_email' => $this->faker->safeEmail(),
            'send_from_name' => $this->faker->name()
        ];
    }
}
