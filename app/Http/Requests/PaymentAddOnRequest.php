<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PaymentAddOnRequest extends FormRequest
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
            'payment_method_uuid' => ['required', 'exists:payment_methods,uuid'],
            'go_back_url' => ['required'],
            'billing_address_uuid' => ['required', 'exists:billing_addresses,uuid'],
            'add_on_subscription_plan_uuid' => ['required', 'exists:add_on_subscription_plans,uuid']
        ];
    }
}
