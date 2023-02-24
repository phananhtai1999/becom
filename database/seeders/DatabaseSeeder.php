<?php

namespace Database\Seeders;

use App\Models\ContactList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

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
            MailTemplateDefaultSeeder::class,
            ConfigSeeder::class
        ]);
        if (!App::environment('production')) {
            $this->call([
                WebsiteSeeder::class,
                MailTemplateSeeder::class,
                SmtpAccountSeeder::class,
                ContactListSeeder::class,
                CampaignSeeder::class
            ]);


            \App\Models\User::factory(50)->create();
            \App\Models\Website::factory(200)->create();
            \App\Models\SmtpAccount::factory(100)->create();
            \App\Models\Contact::factory(100)->create();
            \App\Models\ContactList::factory(100)->create();
            \App\Models\MailTemplate::factory(100)->create();
            \App\Models\Campaign::factory(100)->create();
        }
	}
}
