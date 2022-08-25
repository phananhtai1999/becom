<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class MySmtpAccountRequest extends AbstractRequest
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
            'mail_mailer' => ['required', 'string'],
            'mail_host' => ['required', 'string'],
            'mail_port' => ['required', 'string'],
            'mail_username' => ['required', 'string', 'unique:smtp_accounts,mail_username'],
            'mail_password' => ['required', 'string'],
            'smtp_mail_encryption_uuid' => ['required', 'numeric', 'exists:smtp_account_encryptions,uuid'],
            'mail_from_address' => ['required', 'string', 'email', 'unique:smtp_accounts,mail_from_address'],
            'mail_from_name' => ['required', 'string'],
            'secret_key' => ['required', 'string'],
            'website_uuid' => ['required', 'numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
        ];
    }
}
