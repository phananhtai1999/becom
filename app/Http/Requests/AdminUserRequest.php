<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserRequest extends AbstractRequest
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
            'username' => ['nullable', 'string', "regex:/^(?!.*\.\.)[a-zA-Z0-9]*(?:\.[a-zA-Z0-9]+)*$/", Rule::unique('user_profiles')->whereNull('deleted_at')],
            'first_name' => ['nullable', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['nullable', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('user_profiles')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'regex:/^\S*$/',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'max:255'
            ],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'avatar_img' => ['nullable', 'string'],
            'cover_img' => ['nullable', 'string'],
            'can_add_smtp_account' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => ['numeric', 'min:1', Rule::exists('roles', 'uuid')->where(function ($q) {
                return $q->where('name', '<>', 'root')->whereNull('deleted_at');
            })],
        ];
    }
}
