<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyCampaignRequest extends AbstractRequest
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
            'from_date' => ['date'],
            'to_date' => ['date'],
            'number_email_per_date' => ['numeric'],
            'number_email_per_user' => ['numeric'],
            'status' => ['string'],
            'smtp_account_uuid' => ['numeric', 'min:1', 'exists:smtp_accounts,uuid'],
            'website_uuid' => ['numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'is_running' => ['boolean'],
            'was_finished' => ['boolean'],
            'was_stopped_by_owner' => ['boolean']
        ];
    }
}
