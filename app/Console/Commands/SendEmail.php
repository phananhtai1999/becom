<?php

namespace App\Console\Commands;

use App\Mail\SendMail;
use App\Models\Campaign;
use App\Models\Email;
use App\Models\MailSendingHistory;
use App\Models\MailTemplate;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Send:Email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email by campaigns';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $campaigns = Campaign::where([
            ['from_date', '<', Carbon::now()],
            ['to_date', '>', Carbon::now()],
            ['status', 'active']
        ])->get();

        foreach ($campaigns as $campaign) {
            foreach ($campaign->mailSendingHistories as $value) {
                $a[] = $value['email'];
            }
        }

        if (!empty($a)) {
            $mailSendingHistory = Email::WhereNotIn('email', $a)
                ->get();

            foreach ($mailSendingHistory as $item) {
                Config::set('mail.mailers.smtp.transport', $campaign->smtpAccount->mail_mailer);
                Config::set('mail.mailers.smtp.host', $campaign->smtpAccount->mail_host);
                Config::set('mail.mailers.smtp.port', $campaign->smtpAccount->mail_port);
                Config::set('mail.mailers.smtp.username', $campaign->smtpAccount->mail_username);
                Config::set('mail.mailers.smtp.password', $campaign->smtpAccount->mail_password);
                Config::set('mail.mailers.smtp.encryption', $campaign->smtpAccount->mail_encryption);
                Config::set('mail.from.address', $campaign->smtpAccount->mail_from_address);
                Config::set('mail.from.name', $campaign->smtpAccount->mail_from_name);

                Mail::to($item->email)->send(new SendMail($campaign));

                MailSendingHistory::create([
                    'email' => $item->email,
                    'campaign_uuid' => $campaign->uuid,
                    'time' => Carbon::now()
                ]);
            }
        } else {
            foreach ($campaigns as $campaign) {
                foreach ($campaign->website->emails as $email) {
                    Config::set('mail.mailers.smtp.transport', $campaign->smtpAccount->mail_mailer);
                    Config::set('mail.mailers.smtp.host', $campaign->smtpAccount->mail_host);
                    Config::set('mail.mailers.smtp.port', $campaign->smtpAccount->mail_port);
                    Config::set('mail.mailers.smtp.username', $campaign->smtpAccount->mail_username);
                    Config::set('mail.mailers.smtp.password', $campaign->smtpAccount->mail_password);
                    Config::set('mail.mailers.smtp.encryption', $campaign->smtpAccount->mail_encryption);
                    Config::set('mail.from.address', $campaign->smtpAccount->mail_from_address);
                    Config::set('mail.from.name', $campaign->smtpAccount->mail_from_name);
                    Mail::to($email['email'])->send(new SendMail($campaign));

                    MailSendingHistory::create([
                        'email' => $email['email'],
                        'campaign_uuid' => $campaign->uuid,
                        'time' => Carbon::now()
                    ]);
                }
            }
        }
    }
}
