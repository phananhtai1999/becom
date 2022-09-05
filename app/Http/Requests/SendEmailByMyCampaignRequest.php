<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SendEmailByMyCampaignRequest extends AbstractRequest
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
            'campaign_uuid' => ['required', 'numeric', 'min:1', 'exists:campaigns,uuid'],
            'to_emails' => ['required', 'array'],
            'to_emails.*' => ['required', 'email:rfc,dns'],
            'is_save_history' => ['required', 'boolean']
        ];
    }
}
