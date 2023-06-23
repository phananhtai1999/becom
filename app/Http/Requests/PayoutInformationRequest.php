<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayoutInformationRequest extends FormRequest
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
            'type' => ['required', Rule::in(['bank_account', 'paypal'])],
            'email' => ['required_if:type,===,paypal', 'string'],
            'account_number' => ['required_if:type,===,bank_account', 'string', 'digits:16'],
            'payout_fee' => ['required_if:type,===,bank_account', 'integer'],
            'first_name' => ['required_if:type,===,bank_account', 'string'],
            'last_name' => ['required_if:type,===,bank_account', 'string'],
            'address' => ['required_if:type,===,bank_account', 'string'],
            'city' => ['required_if:type,===,bank_account', 'string'],
            'country' => ['required_if:type,===,bank_account', 'string'],
            'phone' => ['required', 'string'],
            'name_on_account' => ['required_if:type,===,bank_account', 'string'],
            'swift_code' => ['required_if:type,===,bank_account', 'string'],
            'bank_name' => ['required_if:type,===,bank_account', 'string'],
            'bank_address' => ['required_if:type,===,bank_account', 'string'],
            'currency' => ['required_if:type,===,bank_account', 'string']
        ];
    }
}
