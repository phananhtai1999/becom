<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UserConfigRequest extends AbstractRequest
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
            'user_uuid' => ['required', 'numeric', 'min:1', 'exists:user_profiles,uuid'],
            'app_language' => ['required', 'string'],
            'user_language' => ['required', 'string'],
            'display_name_style' => ['required', 'numeric', 'min:1'],
        ];
    }
}
