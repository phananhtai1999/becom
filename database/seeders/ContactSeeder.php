<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\Position;
use App\Models\Remind;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        $contactList = ContactList::where('user_uuid', $user->uuid)->inRandomOrder()->first();
        $status = Status::factory()->create(['user_uuid' => $user->uuid]);
        $position = Position::factory()->create(['user_uuid' => $user->uuid]);
        $company = Company::factory()->create(['user_uuid' => $user->uuid]);
        $remind = Remind::factory()->create(['user_uuid' => $user->uuid]);

        $contacts = Contact::factory(3)->create([
            'user_uuid' => $user->uuid,
            'status_uuid' => $status->pluck('uuid')[0]
        ]);

        foreach ($contacts as $contact) {
            $contact->contactLists()->sync([$contactList->uuid]);
            $contact->companies()->sync([$company->pluck('uuid')[0] => ['position_uuid' => $position->pluck('uuid')[0]]]);
            $contact->reminds()->sync([$remind->pluck('uuid')[0]]);
        }
    }
}
