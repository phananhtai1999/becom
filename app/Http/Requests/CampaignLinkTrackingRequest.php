<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class CampaignLinkTrackingRequest extends AbstractRequest
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
            'to_url' => ['required', 'numeric', 'min:1']
        ];
    }
}
