<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateUserRequest extends AbstractRequest
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
            'username' => ['string', 'unique:users,username'],
            'email' => ['string', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['string', 'confirmed'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'avatar_img' => ['nullable', 'string'],
            'cover_img' => ['nullable', 'string'],
            'can_add_smtp_account' => ['nullable', 'boolean'],
            'roles' => ['array', 'min:1'],
            'roles.*' => ['numeric', 'min:1', 'exists:roles,uuid'],
        ];
    }
}
