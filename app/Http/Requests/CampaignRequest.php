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
            'from_date' => ['required', 'date', 'before_or_equal:to_date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'number_email_per_date' => ['required', 'numeric', 'min:1', 'lte:number_email_per_user'],
            'number_email_per_user' => ['required', 'numeric', 'min:1', 'gte:number_email_per_date'],
            'status' => ['required', 'string', 'in:active,banned'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', 'exists:smtp_accounts,uuid'],
            'website_uuid' => ['required', 'numeric', 'min:1', 'exists:websites,uuid'],
            'was_finished' => ['required', 'boolean'],
            'was_stopped_by_owner' => ['required', 'boolean'],
            'user_uuid' => ['nullable', 'numeric', 'min:1', 'exists:users,uuid'],
            'contact_list' => ['required', 'array', 'min:1'],
            'contact_list.*' => ['required', 'numeric', 'min:1', 'exists:contact_lists,uuid'],
        ];
    }
}
