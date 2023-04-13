<?php

namespace Database\Seeders;

use App\Models\Email;
use Illuminate\Database\Seeder;

class MoveValueWebsiteUuidToWebsiteEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emails = Email::all();
        foreach ($emails as $email){
            $email->sendProjects()->attach($email->send_project_uuid);
        }

    }
}
