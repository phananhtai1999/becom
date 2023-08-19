<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class DomainForUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();

        $domain = [
            [
                'name' => 'techupcorp.us',
                'owner_uuid' => optional($user)->uuid,
                'active_mailbox' => false,
            ]
        ];

        foreach ($domain as $value) {
            Domain::firstOrCreate($value);
        }
    }
}
