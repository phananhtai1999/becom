<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\SmtpAccount;
use Illuminate\Support\Facades\Config;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SmtpAccountService extends AbstractService
{
    protected $modelClass = SmtpAccount::class;

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findSmtpAccount()
    {
        return $this->model->where('uuid', request()->get('smtp_account_uuid'))->first();
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendEmailsBySmtpAccount()
    {
        $smtpAccount = $this->findSmtpAccount();

        Config::set('mail.mailers.smtp.transport', $smtpAccount->mail_mailer);
        Config::set('mail.mailers.smtp.host', $smtpAccount->mail_host);
        Config::set('mail.mailers.smtp.port', $smtpAccount->mail_port);
        Config::set('mail.mailers.smtp.username', $smtpAccount->mail_username);
        Config::set('mail.mailers.smtp.password', $smtpAccount->mail_password);
        Config::set('mail.mailers.smtp.encryption', $smtpAccount->mail_encryption);
        Config::set('mail.from.address', $smtpAccount->mail_from_address);
        Config::set('mail.from.name', $smtpAccount->mail_from_name);
    }
}
