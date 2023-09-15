<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SendProject;
use Illuminate\Database\Seeder;

class SendProjectSeeder extends Seeder
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

        SendProject::factory(5)->create([
            'user_uuid' => optional($user)->uuid
        ]);
        SendProject::factory(5)->create([
            'user_uuid' => optional($admin)->uuid
        ]);
    }
}
