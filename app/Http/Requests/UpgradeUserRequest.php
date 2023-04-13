<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UpgradeUserRequest extends AbstractRequest
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
        $rule = [
            'payment_method_uuid' => ['required', Rule::in('1', '2')],
            'go_back_url' => ['required'],
            'billing_address_uuid' => ['required', 'exists:billing_addresses,uuid'],
            "card_number" => ['required_if:payment_method,==,2', 'integer', 'digits:16'],
            "exp_month" => ['required_if:payment_method,==,2', 'integer'],
            "exp_year" => ['required_if:payment_method,==,2', 'integer', 'min:' . Carbon::now()->year],
            "cvc" => ['required_if:payment_method,==,2', 'integer', 'digits:3'],
            "subscription_plan_uuid" => ['required', 'integer', 'exists:subscription_plans,uuid']
        ];

        if ($this->request->get('exp_year') == Carbon::now()->year) {
            $rule['exp_month'] = array_merge($rule['exp_month'], ['min:' . Carbon::now()->month]);
        }

        return $rule;
    }
}
