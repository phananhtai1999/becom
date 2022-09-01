<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmtpAccountEncryption;
class SmtpAccountEncryptionSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
		$encryptions = [
			[
				'name' => 'SSL',
			],
			[
				'name' => 'LTS',
			],

		];

		foreach ($encryptions as $encryption) {
			SmtpAccountEncryption::firstOrCreate($encryption);
		}
	}
}
