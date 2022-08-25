<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SendEmailByCampaignRequest extends AbstractRequest
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
            'campaign_uuid' => ['required', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query){
                return $query->where([
                    ['uuid', $this->request->get('campaign_uuid')],
                    ['from_date', '<=', Carbon::now()],
                    ['to_date', '>=', Carbon::now()],
                    ['was_finished', false],
                    ['was_stopped_by_owner', false],
                ]);
            })],
            'to_emails' => ['required', 'array'],
            'to_emails.*' => ['required', 'email:rfc,dns'],
            'is_save_history' => ['required', 'boolean']
        ];
    }
}