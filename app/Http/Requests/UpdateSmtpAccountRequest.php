<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateSmtpAccountRequest extends AbstractRequest
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
            'mail_mailer' => ['string'],
            'mail_host' => ['string'],
            'mail_port' => ['string'],
            'mail_username' => ['string', 'unique:smtp_accounts,mail_username,'.$this->id .',uuid'],
            'mail_password' => ['string'],
            'smtp_mail_encryption_uuid' => ['numeric', 'exists:smtp_account_encryptions,uuid'],
            'mail_from_address' => ['string'],
            'mail_from_name' => ['string'],
            'secret_key' => ['string'],
            'website_uuid' => ['string', 'exists:websites,uuid'],
        ];
    }
}
