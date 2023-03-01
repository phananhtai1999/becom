<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionPlanRequest extends FormRequest
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
            'duration_type' => ['required', Rule::in(['month', 'year'])],
            'duration' => ['required', 'integer', 'min:1', 'max:12'],
            'platform_package_uuid' => ['required', 'exists:platform_packages,uuid'],
        ];
    }
}