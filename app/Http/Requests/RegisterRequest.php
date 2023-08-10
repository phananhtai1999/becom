<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends AbstractRequest
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
            'username' => ['nullable','string',"regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/", Rule::unique('users')->whereNull('deleted_at'),],
            'first_name' => ['required', 'string',"regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['required', 'string',"regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('users')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
                'max:255'
            ]
        ];
    }
}
