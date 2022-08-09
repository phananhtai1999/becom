<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateCampaignRequest extends AbstractRequest
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
            'tracking_key' => ['string'],
            'mail_template_uuid' => ['numeric', 'min:1', 'exists:mail_templates,uuid'],
            'from_date' => ['date', 'before_or_equal:to_date'],
            'to_date' => ['date', 'after_or_equal:from_date'],
            'number_email_per_date' => ['numeric', 'min:1'],
            'number_email_per_user' => ['numeric', 'min:1'],
            'status' => ['string'],
            'smtp_account_uuid' => ['numeric', 'min:1', 'exists:smtp_accounts,uuid'],
            'website_uuid' => ['numeric', 'min:1', 'exists:websites,uuid'],
            'is_running' => ['boolean'],
            'was_finished' => ['boolean'],
            'was_stopped_by_owner' => ['boolean']
        ];
    }
}
