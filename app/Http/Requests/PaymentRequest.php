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
        return [
            'payment_method_uuid' => ['required', 'exists:payment_methods,uuid'],
            'go_back_url' => ['required'],
            'credit_package_uuid' => ['required', 'exists:credit_packages,uuid'],
        ];
    }
    
}
