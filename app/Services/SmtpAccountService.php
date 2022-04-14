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
     * @param $perPage
     * @return mixed
     */
    public function indexMySmtpAccount($perPage)
    {
        return $this->model->select('smtp_accounts.*')
            ->join('websites', 'websites.uuid', '=', 'smtp_accounts.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findMySmtpAccountByKeyOrAbort($id)
    {
        $smtpAccount = $this->model->select('smtp_accounts.*')
            ->join('websites', 'websites.uuid', '=', 'smtp_accounts.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('smtp_accounts.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $smtpAccount;
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function deleteMySmtpAccountByKey($id)
    {
        $smtpAccount = $this->model->select('smtp_accounts.*')
            ->join('websites', 'websites.uuid', '=', 'smtp_accounts.website_uuid')
            ->join('users', 'users.uuid', '=', 'websites.user_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey())
            ->where('smtp_accounts.uuid', $id)
            ->first();

        if (!empty($smtpAccount)) {
            return $this->destroy($smtpAccount->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }

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
