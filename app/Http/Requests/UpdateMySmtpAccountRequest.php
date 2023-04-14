<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMySmtpAccountRequest extends AbstractRequest
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
            'mail_mailer' => ['string', 'in:smtp,telegram,viber'],
            'mail_host' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_port' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_username' => ['required_if:mail_mailer,===,smtp', 'string', Rule::unique('smtp_accounts')->ignore($this->id, 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'mail_password' => ['required_if:mail_mailer,===,smtp', 'string'],
            'smtp_mail_encryption_uuid' => ['numeric', 'exists:smtp_account_encryptions,uuid'],
            'mail_from_address' => ['required_if:mail_mailer,===,smtp', 'string'],
            'mail_from_name' => ['required_if:mail_mailer,===,smtp', 'string'],
            'secret_key' => ['required_if:mail_mailer,===,telegram,viber', 'string'],
            'send_project_uuid' => ['numeric', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
        ];
    }
}
