<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateUserDetailRequest extends AbstractRequest
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
            'user_uuid' => ['numeric', 'min:1', 'exists:user_profiles,uuid'],
            'about' => ['nullable', 'string'],
            'gender' => ['numeric', 'max:1'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
