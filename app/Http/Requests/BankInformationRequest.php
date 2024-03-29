<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankInformationRequest extends AbstractRequest
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
            'swift_code' => ['required', 'string', Rule::unique('bank_informations', 'swift_code')->whereNull('deleted_at')],
            'bank_name' => ['required', 'string'],
            'bank_address' => ['required', 'string'],
            'is_verified' => ['boolean'],
            'currency' => ['required', 'string']
        ];
    }
}
