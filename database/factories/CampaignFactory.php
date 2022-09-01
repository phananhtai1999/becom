<?php

namespace Database\Factories;

use App\Models\Website;
use App\Models\MailTemplate;
use App\Models\SmtpAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'tracking_key' => $this->faker->lexify('tracking-??????'),
			'mail_template_uuid' => MailTemplate::inRandomOrder()->first()->uuid,
			'from_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
			'to_date' => $this->faker->dateTimeBetween('now', '+1 week'),
			'number_email_per_date' => rand(1,10),
			'number_email_per_user' => rand(1,10),
			'smtp_account_uuid' => SmtpAccount::inRandomOrder()->first()->uuid,
			'was_finished' => rand(0,1),
			'status' => 'active',
			'was_stopped_by_owner' => rand(0,1),
			'website_uuid' => Website::where('uuid', '<=', 3)->inRandomOrder()->first()->uuid,
		];
	}
}
