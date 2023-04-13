<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
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
        return [
            'mail_mailer' => ['required', 'string', 'in:smtp,telegram,viber'],
            'mail_host' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_port' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_username' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_password' => ['required_if:mail_mailer,===,smtp', 'string'],
            'smtp_mail_encryption_uuid' => ['required', 'numeric', 'exists:smtp_account_encryptions,uuid'],
            'mail_from_address' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_from_name' => ['required_if:mail_mailer,===,smtp', 'string'],
            'secret_key' => ['required_if:mail_mailer,===,telegram,viber', 'string'],
            'send_project_uuid' => ['nullable', 'numeric', 'min:1', 'exists:send_projects,uuid']
        ];
    }
}
