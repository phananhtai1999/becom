<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateContactRequest extends AbstractRequest
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
            'email' => ['string'],
            'first_name' => ['string'],
            'last_name' => ['string'],
            'middle_name' => ['string'],
            'phone' => ['numeric'],
            'dob' => ['date_format:Y-m-d'],
            'sex' => ['string'],
            'city' => ['string'],
            'country' => ['string'],
        ];
    }
}
