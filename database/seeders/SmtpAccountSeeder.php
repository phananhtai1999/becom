<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use App\Models\SmtpAccount;
use App\Models\SmtpAccountEncryption;
use App\Models\User;
use Illuminate\Database\Seeder;

class SmtpAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $smtpAccounts = [
            [
                "mail_username"=> "phongphunguyen7575a@gmail.com",
                "mail_password"=> "ktwpnxofevcqnsze",
                "mail_from_address"=> "phongphunguyen7575a@gmail.com",
            ],
            [
                "mail_username"=> "phongphunguyen7575b@gmail.com",
                "mail_password"=> "cnneowuovtkbvagi",
                "mail_from_address"=> "phongphunguyen7575b@gmail.com",
            ]
        ];

        $user = User::where('email', 'user1@sendemail.techupcorp')->first();

        foreach ($smtpAccounts as $smtpAccount) {
            $mailTemplate = MailTemplate::where('user_uuid', $user->uuid)->where('type', 'email')->inRandomOrder()->first();
            $smtp = SmtpAccount::where([
                ['mail_username', $smtpAccount['mail_username']],
                ['user_uuid', $user->uuid]
            ])->first();
            if (!$smtp) {
                SmtpAccount::factory()->create([
                    "mail_mailer"=> "smtp",
                    "mail_host"=> "smtp.gmail.com",
                    "mail_port"=> 465,
                    "mail_username"=> $smtpAccount['mail_username'],
                    "mail_password"=> $smtpAccount['mail_password'],
                    "smtp_mail_encryption_uuid"=> SmtpAccountEncryption::where('name', 'SSL')->first()->uuid,
                    "mail_from_address"=> $smtpAccount['mail_from_address'],
                    "secret_key"=> "smtp",
                    "user_uuid"=> $user->uuid,
                    'website_uuid' => $mailTemplate->website_uuid
                ]);
            }else{
                $smtp->update([
                    'website_uuid' => $mailTemplate->website_uuid
                ]);
            }
        }
    }
}
