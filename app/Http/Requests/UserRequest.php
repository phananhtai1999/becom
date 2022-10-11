<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UserRequest extends AbstractRequest
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
            'username' => ['required', 'string', 'unique:users,username'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
            'avatar_img' => ['nullable', 'string'],
            'cover_img' => ['nullable', 'string'],
            'can_add_smtp_account' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => ['numeric', 'min:1', 'exists:roles,uuid'],
        ];
    }
}
