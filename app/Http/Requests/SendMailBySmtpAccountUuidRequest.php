<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class SendMailBySmtpAccountUuidRequest extends AbstractRequest
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
            'smtp_account_uuid' => ['required', 'numeric', 'min:1', 'exists:smtp_accounts,uuid'],
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'to_emails' => ['required', 'array'],
            'to_emails.*' => ['email:rfc,dns'],
        ];
    }
}
