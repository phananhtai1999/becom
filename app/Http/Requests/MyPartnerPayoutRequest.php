<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class MyPartnerPayoutRequest extends AbstractRequest
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
            'amount' => ['required', 'numeric', 'min:0'],
            'payout_information_uuid' => ['required', 'exists:payout_informations,uuid']
        ];
    }
}
