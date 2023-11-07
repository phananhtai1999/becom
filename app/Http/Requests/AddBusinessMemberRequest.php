<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddBusinessMemberRequest extends FormRequest
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
            'business_uuid' => ['required', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')],
            'user_uuids' => ['required', 'array', 'min:1'],
            'user_uuid.*' => ['required', 'integer', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
