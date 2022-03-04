<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateMailSendingHistoryRequest extends AbstractRequest
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
            'campaign_uuid' => ['numeric', 'min:1'],
            'email' => ['string'],
            'time' => ['date'],
        ];
    }
}
