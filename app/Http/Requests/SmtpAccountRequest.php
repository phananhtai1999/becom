<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SmtpAccount;
use Illuminate\Validation\Rule;

class SmtpAccountRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $smtpMailMailer = SmtpAccount::SMTP_ACCOUNT_SMTP_MAIL_MAILER;
        $telegramMailMailer = SmtpAccount::SMTP_ACCOUNT_TELEGRAM_MAIL_MAILER;
        $viberMailMailer = SmtpAccount::SMTP_ACCOUNT_VIBER_MAIL_MAILER;

        return [
            'mail_mailer' => ['required', 'string', Rule::in([$smtpMailMailer, $telegramMailMailer, $viberMailMailer])],
            'mail_host' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'mail_port' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'mail_username' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'mail_password' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'smtp_mail_encryption_uuid' => ['required', 'integer', 'exists:smtp_account_encryptions,uuid'],
            'mail_from_address' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'mail_from_name' => ["required_if:mail_mailer,===,$smtpMailMailer", 'string'],
            'secret_key' => ["required_if:mail_mailer,===,$telegramMailMailer,$viberMailMailer", 'string'],
            'send_project_uuid' => ['nullable', 'integer', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')]
        ];
    }
}
