<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
     * @return string[][]
     */
    public function rules(): array
    {
        $rule = [
            'payment_method_uuid' => ['required', 'exists:payment_methods,uuid'],
            'go_back_url' => ['required'],
            'credit_package_uuid' => ['required', 'exists:credit_packages,uuid'],
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

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'exp_month.min' => 'The exp month must be as least current month',
            'exp_year.min' => 'The exp year must be as least current year'
        ];
    }
}
