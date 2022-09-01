<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
		$this->call([
			UserSeeder::class,
			SmtpAccountEncryptionSeeder::class,

		]);


		\App\Models\Website::factory(200)->create();
		\App\Models\Email::factory(100)->create();
		\App\Models\SmtpAccount::factory(100)->create();
		\App\Models\MailTemplate::factory(100)->create();
		\App\Models\Campaign::factory(100)->create();
		
	}
}
