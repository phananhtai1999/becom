<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        for ($i = 1; $i < 10; $i++) {
            MailTemplate::factory()->create([
                'website_uuid' => Website::where('user_uuid', $user->uuid)->inRandomOrder()->first()->uuid,
                'user_uuid' => $user->uuid,
            ]);
        }
    }
}
