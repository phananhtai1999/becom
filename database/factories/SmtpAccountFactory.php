<?php

namespace Database\Factories;

use App\Models\SmtpAccountEncryption;
use App\Models\User;
use App\Models\SendProject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmtpAccountFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
        $optionMailMailer = array(
            'smtp',
            'telegram',
            'viber',
        );
		return [
			'mail_mailer' => $optionMailMailer[array_rand($optionMailMailer)],
			'mail_host' => $this->faker->domainWord() .'.' . $this->faker->domainWord() .'.'. $this->faker->domainName(),
			'mail_port' => 587,
			'mail_username' => $this->faker->userName(),
			'mail_password' => 'password',
			'smtp_mail_encryption_uuid' => SmtpAccountEncryption::inRandomOrder()->first()->uuid,
			'mail_from_address' => $this->faker->safeEmail(),
			'mail_from_name' => $this->faker->name(),
			'secret_key' => $this->faker->lexify('key-????????'),
			'send_project_uuid' => SendProject::inRandomOrder()->first()->uuid,
            'user_uuid' => User::inRandomOrder()->first()->uuid,
            'status' => 'work',
            'publish' => true
		];
	}
}
