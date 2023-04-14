<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\ContactList;
use App\Models\MailTemplate;
use App\Models\SmtpAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();

        $smtpAccounts = SmtpAccount::where(function ($query) {
            $query->where("mail_username", "phongphunguyen7575a@gmail.com")
                ->orWhere("mail_username", "phongphunguyen7575b@gmail.com");
        })->where('user_uuid', $user->uuid)->get();

        foreach ($smtpAccounts as $smtpAccount) {
            for ($i = 1 ; $i < 5 ; $i++) {
                $contactLists = ContactList::where('user_uuid', $user->uuid)->inRandomOrder()->limit(2)->get();
                $mailTemplate = MailTemplate::where([
                    ["send_project_uuid", $smtpAccount->send_project_uuid],
                    ['user_uuid', $user->uuid]
                ])->inRandomOrder()->first();

                $campaign = Campaign::factory()->create([
                    'mail_template_uuid' => $mailTemplate->uuid,
                    'smtp_account_uuid' => $smtpAccount->uuid,
                    'was_finished' => false,
                    'was_stopped_by_owner' => true,
                    'send_project_uuid' => $smtpAccount->send_project_uuid,
                    'send_type' => $mailTemplate->type,
                    'user_uuid' => $user->uuid,
                ]);

                $campaign->contactLists()->sync($contactLists);
            }
        }
    }
}
