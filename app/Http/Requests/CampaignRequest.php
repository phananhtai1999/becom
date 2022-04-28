<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class CampaignRequest extends AbstractRequest
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
            'tracking_key' => ['required', 'string'],
            'mail_template_uuid' => ['required', 'numeric', 'min:1', 'exists:mail_templates,uuid'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
            'number_email_per_date' => ['required', 'numeric'],
            'number_email_per_user' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'smtp_account_uuid' => ['required', 'numeric', 'min:1', 'exists:smtp_accounts,uuid'],
            'website_uuid' => ['required', 'numeric', 'min:1', 'exists:websites,uuid'],
            'is_running' => ['required', 'boolean'],
            'was_finished' => ['required', 'boolean'],
            'was_stopped' => ['required', 'boolean']
        ];
    }
}
