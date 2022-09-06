<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class ContactRequest extends AbstractRequest
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
            'email' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'middle_name' => ['required', 'string'],
            'phone' => ['required', 'numeric'],
            'dob' => ['required', 'date_format:Y-m-d'],
            'sex' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
        ];
    }
}
