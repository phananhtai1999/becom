<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetContactListRequest extends FormRequest
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
            'user_uuid' => ['required', 'integer', 'min:1', 'exists:users,uuid'],
            'team_uuid' => ['required', 'integer', 'min:1', 'exists:teams,uuid'],
            'contact_list_uuids' => ['array'],
            'contact_list_uuids.*' => ['integer', 'exists:contact_lists,uuid'],
        ];
    }
}
