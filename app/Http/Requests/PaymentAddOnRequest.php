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
        $rule = [
            'payment_method_uuid' => ['required', 'exists:payment_methods,uuid'],
            'go_back_url' => ['required'],
            'add_on_subscription_plan_uuid' => ['required', 'exists:add_on_subscription_plans,uuid'],
            "card_number" => ['required_if:payment_method,==,stripe', 'integer', 'digits:16'],
            "exp_month" => ['required_if:payment_method,==,stripe', 'integer'],
            "exp_year" => ['required_if:payment_method,==,stripe', 'integer', 'min:' . Carbon::now()->year],
            "cvc" => ['required_if:payment_method,==,stripe', 'integer', 'digits:3']
        ];

        if ($this->request->get('exp_year') == Carbon::now()->year) {
            $rule['exp_month'] = array_merge($rule['exp_month'], ['min:' . Carbon::now()->month]);

        }

        return $rule;
    }
}
