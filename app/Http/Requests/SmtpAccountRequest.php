<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

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
            'mail_mailer' => ['required', 'string'],
            'mail_host' => ['required', 'string'],
            'mail_port' => ['required', 'string'],
            'mail_username' => ['required', 'string'],
            'mail_password' => ['required', 'string'],
            'mail_encryption' => ['required', 'string'],
            'mail_from_address' => ['required', 'string'],
            'mail_from_name' => ['required', 'string'],
            'secret_key' => ['required', 'string'],
            'website_uuid' => ['required', 'string', 'exists:websites,uuid'],
        ];
    }
}
