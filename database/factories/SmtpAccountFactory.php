<?php

namespace Database\Factories;

use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmtpAccountFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'mail_mailer' => $this->faker->lexify('mail-????'),
			'mail_host' => $this->faker->domainWord() .'.' . $this->faker->domainWord() .'.'. $this->faker->domainName(),
			'mail_port' => 587,
			'mail_username' => $this->faker->userName(),
			'mail_password' => 'password',
			'smtp_mail_encryption_uuid' => 1,
			'mail_from_address' => $this->faker->safeEmail(),
			'mail_from_name' => $this->faker->name(),
			'secret_key' => $this->faker->lexify('key-????????'),
			'website_uuid' => Website::where('uuid', '<=', 3)->inRandomOrder()->first()->uuid,
		];
	}
}
