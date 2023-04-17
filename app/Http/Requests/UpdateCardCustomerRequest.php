<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCardCustomerRequest extends FormRequest
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
            "card_number" => ['required', 'integer', 'digits:16'],
            "exp_month" => ['required', 'integer'],
            "exp_year" => ['required', 'integer', 'min:' . Carbon::now()->year],
            "cvc" => ['required', 'integer', 'digits:3'],
            "type" => ['required', Rule::in(['update', 'add'])],
        ];

        if ($this->request->get('exp_year') == Carbon::now()->year) {
            $rule['exp_month'] = array_merge($rule['exp_month'], ['min:' . Carbon::now()->month]);
        }

        return $rule;
    }
}
