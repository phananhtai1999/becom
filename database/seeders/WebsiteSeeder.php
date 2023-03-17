<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        $admin = User::where('email', 'admin@sendemail.techupcorp')->first();

        Website::factory(5)->create([
            'user_uuid' => $user->uuid
        ]);
        Website::factory(5)->create([
            'user_uuid' => $admin->uuid
        ]);
    }
}
