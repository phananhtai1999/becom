<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateEmailRequest extends AbstractRequest
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
            'email' => ['string', 'email:rfc,dns', 'unique:emails,email'],
            'age' => ['numeric', 'min:1'],
            'first_name' => ['string'],
            'last_name' => ['string'],
            'country' => ['string'],
            'city' => ['string'],
            'job' => ['string'],
            'websites' => ['array', 'min:1'],
            'websites.*' => ['numeric', 'min:1', 'exists:websites,uuid'],
        ];
    }
}
