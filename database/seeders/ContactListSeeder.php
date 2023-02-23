<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();

        $contactLists = ContactList::factory(5)->create([
            'user_uuid' => $user->uuid
        ]);

        foreach ($contactLists as $contactList) {
            Contact::factory(5)->create([
                'user_uuid' => $user->uuid,
            ])->each(function ($contact) use ($contactList) {
                $contact->contactLists()->sync([$contactList->uuid]);
            });
        }
    }
}
