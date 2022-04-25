<?php

namespace Database\Seeders;

use App\Models\SmtpAccount;
use App\Models\SmtpAccountEncryption;
use Illuminate\Database\Seeder;

class ChangeValueMailEncryptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $smtpAccounts = SmtpAccount::all();
        foreach ($smtpAccounts as $smtpAccount ){
            $smtpAccountEncryption = SmtpAccountEncryption::firstOrCreate(
                ['name' => $smtpAccount->mail_encryption]);
            SmtpAccount::where('uuid', $smtpAccount->uuid)->update(['mail_encryption' => $smtpAccountEncryption->uuid]);
        }
    }
}
