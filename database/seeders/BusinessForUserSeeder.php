<?php

namespace Database\Seeders;

use App\Models\BusinessManagement;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessForUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        $domain = Domain::where([
            ['name', 'techupcorp.us'],
            ['owner_uuid', optional($user)->uuid],
        ])->first();

        $business = BusinessManagement::where('owner_uuid', optional($user)->uuid)->first();
        if (!$business) {
            BusinessManagement::factory()->create([
                'domain_uuid' => optional($domain)->uuid,
                'owner_uuid' => optional($user)->uuid,
            ]);
        } else {
            $business->update([
                'domain_uuid' => optional($domain)->uuid
            ]);
            $domain->update([
                'business_uuid' => $business->uuid
            ]);
        }
    }
}
