<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        $contact = Contact::where('user_uuid', optional($user)->uuid)->first();
        Note::factory(3)->create([
            'user_uuid' => $user->uuid,
            'contact_uuid' => $contact->uuid
        ]);
    }
}
