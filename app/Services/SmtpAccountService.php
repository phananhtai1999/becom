<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SmtpAccountQueryBuilder;
use App\Models\SmtpAccount;
use Illuminate\Support\Facades\Config;

class SmtpAccountService extends AbstractService
{
    protected $modelClass = SmtpAccount::class;

    protected $modelQueryBuilderClass = SmtpAccountQueryBuilder::class;

    /**
     * @param $smtpAccount
     * @return void
     */
    public function setSmtpAccountForSendEmail($smtpAccount)
    {
        Config::set('mail.mailers.smtp.transport', $smtpAccount->mail_mailer);
        Config::set('mail.mailers.smtp.host', $smtpAccount->mail_host);
        Config::set('mail.mailers.smtp.port', $smtpAccount->mail_port);
        Config::set('mail.mailers.smtp.username', $smtpAccount->mail_username);
        Config::set('mail.mailers.smtp.password', $smtpAccount->mail_password);
        Config::set('mail.mailers.smtp.encryption', $smtpAccount->smtpAccountEncryption->name);
        Config::set('mail.from.address', $smtpAccount->mail_from_address);
        Config::set('mail.from.name', $smtpAccount->mail_from_name);
    }
}
