<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankInformationRequest extends FormRequest
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
            'swift_code' => ['required', 'string', 'unique:bank_informations,swift_code'],
            'bank_name' => ['required', 'string'],
            'bank_address' => ['required', 'string'],
            'currency' => ['required', 'string']
        ];
    }
}
